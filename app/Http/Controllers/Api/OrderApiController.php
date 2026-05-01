<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{  
    /** 
     * Display a listing of orders.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $orders = Order::with(['user', 'cartItems.product', 'shippingMethod'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Siparişler başarıyla getirildi',
                'data' => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Siparişler getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Token sahibinin user_id'sini al
            $userId = $request->user()->id;
            
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_surname' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'city' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'shipping_address' => 'required|string',
                'payment_method' => 'required|string|max:50',
                'carts_ids' => 'required|array',
                'carts_ids.*' => 'required|integer|exists:carts,id',
                'discount_amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|integer|in:0,1,2,3', // Default 0
                'notes' => 'nullable|string',
                'api_archive_code' => 'nullable|string|max:100',
                // Sipariş ZIP url (R2 public link veya başka URL)
                's3_zip' => 'nullable|string|url|max:1000',
                // Kargo metodu (admin panelden tanımlı)
                'shipping_method_id' => 'nullable|integer|exists:shipping_methods,id',
                'shipping_cost' => 'nullable|numeric|min:0',
                // Fatura adresi alanları
                'billing_same_as_shipping' => 'nullable|boolean',
                'billing_name' => 'nullable|string|max:255',
                'billing_surname' => 'nullable|string|max:255',
                'billing_phone' => 'nullable|string|max:20',
                'billing_city' => 'nullable|string|max:100',
                'billing_district' => 'nullable|string|max:100',
                'billing_address' => 'nullable|string',
                'billing_company' => 'nullable|string|max:255',
                'billing_tax_no' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cart'ları kontrol et - token sahibine ait mi?
            $carts = Cart::whereIn('id', $request->carts_ids)
                ->where('user_id', $userId)
                ->get();

            if ($carts->count() !== count($request->carts_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bazı cart ID\'leri bu kullanıcıya ait değil veya bulunamadı'
                ], 422);
            }

            // Cart'ların price değerlerini topla (price * quantity)
            $totalPrice = $carts->sum(function($cart) {
                return $cart->price * $cart->quantity;
            });

            // Kargo ücretini topla
            $shippingCost = (float) ($request->shipping_cost ?? 0);
            $totalPrice += $shippingCost;

            // Fatura adresi: same_as_shipping=true (default) ise teslimat alanlarını kopyala
            $billingSame = $request->boolean('billing_same_as_shipping', true);
            if ($billingSame) {
                $billingFields = [
                    'billing_name' => $request->customer_name,
                    'billing_surname' => $request->customer_surname,
                    'billing_phone' => $request->customer_phone,
                    'billing_city' => $request->city,
                    'billing_district' => $request->district,
                    'billing_address' => $request->shipping_address,
                    'billing_company' => $request->input('billing_company'),
                    'billing_tax_no' => $request->input('billing_tax_no'),
                ];
            } else {
                $billingFields = [
                    'billing_name' => $request->billing_name,
                    'billing_surname' => $request->billing_surname,
                    'billing_phone' => $request->billing_phone,
                    'billing_city' => $request->billing_city,
                    'billing_district' => $request->billing_district,
                    'billing_address' => $request->billing_address,
                    'billing_company' => $request->billing_company,
                    'billing_tax_no' => $request->billing_tax_no,
                ];
            }

            // Sipariş numarası oluştur (ken-000000001 formatında)
            $orderNumber = Order::generateOrderNumber();

            $order = Order::create(array_merge([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'api_archive_code' => $request->api_archive_code,
                'customer_name' => $request->customer_name,
                'customer_surname' => $request->customer_surname,
                'customer_phone' => $request->customer_phone,
                'city' => $request->city,
                'district' => $request->district,
                'shipping_address' => $request->shipping_address,
                'shipping_method_id' => $request->shipping_method_id,
                'shipping_cost' => $shippingCost,
                'billing_same_as_shipping' => $billingSame,
                's3_zip' => $request->s3_zip,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'total_price' => $totalPrice,
                'discount_amount' => $request->discount_amount ?? 0,
                'status' => $request->status ?? 0,
            ], $billingFields));

            // Cart'ların order_id ve cart_id alanlarını güncelle, S3 dosyasını rename et
            foreach ($carts as $cart) {
                $oldCartId = $cart->cart_id;
                $newCartId = $cart->generateCartIdentifier($orderNumber);

                $cart->update([
                    'order_id' => $order->id,
                    'cart_id' => $newCartId,
                ]);

                // S3'teki ZIP dosyasını yeni isimle güncelle (dosya adı + içindeki klasör)
                $cart->renameS3Zip($oldCartId, $newCartId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla oluşturuldu',
                'data' => $order->load(['user', 'cartItems', 'shippingMethod'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = Order::with(['user', 'cartItems.product', 'cartItems.customizationParamsCustomers.customizationParam', 'shippingMethod'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Sipariş detayları getirildi',
                'data' => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified order.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'customer_name' => 'sometimes|string|max:255',
                'customer_surname' => 'sometimes|string|max:255',
                'customer_phone' => 'sometimes|string|max:20',
                'city' => 'sometimes|string|max:100',
                'district' => 'sometimes|string|max:100',
                'shipping_address' => 'sometimes|string',
                'payment_method' => 'sometimes|string|max:50',
                'total_price' => 'sometimes|numeric|min:0',
                'discount_amount' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|integer|in:0,1,2,3',
                'notes' => 'nullable|string',
                'api_archive_code' => 'nullable|string|max:100',
                's3_zip' => 'sometimes|nullable|string|url|max:1000',
                'shipping_method_id' => 'sometimes|nullable|integer|exists:shipping_methods,id',
                'shipping_cost' => 'sometimes|nullable|numeric|min:0',
                'billing_same_as_shipping' => 'sometimes|boolean',
                'billing_name' => 'sometimes|nullable|string|max:255',
                'billing_surname' => 'sometimes|nullable|string|max:255',
                'billing_phone' => 'sometimes|nullable|string|max:20',
                'billing_city' => 'sometimes|nullable|string|max:100',
                'billing_district' => 'sometimes|nullable|string|max:100',
                'billing_address' => 'sometimes|nullable|string',
                'billing_company' => 'sometimes|nullable|string|max:255',
                'billing_tax_no' => 'sometimes|nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order->update($request->except('order_number'));

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla güncellendi',
                'data' => $order->load(['user', 'cartItems', 'shippingMethod'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified order.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla silindi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order items (cart items)
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getOrderItems(int $orderId): JsonResponse
    {
        try {
            $order = Order::findOrFail($orderId);
            $cartItems = $order->cartItems()->with(['product', 'customizationParamsCustomers.customizationParam'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemleri getirildi',
                'data' => $cartItems
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemleri getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

