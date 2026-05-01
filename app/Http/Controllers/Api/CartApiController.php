<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\CustomizationPivotParam;
use App\Models\DiscountGroup;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class CartApiController extends Controller
{ 
    /** 
     * Display a listing of cart items.
     *
     * @param Request $request   
     * @return JsonResponse 
     */
    public function index(Request $request): JsonResponse
    { 
        try {
            $perPage = $request->get('per_page', 15);
            $query = Cart::with(['user', 'product', 'order', 'discountGroup', 'currentStatus']);

            // Filtreleme
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('barcode')) {
                $query->where('barcode', 'LIKE', '%' . $request->barcode . '%');
            }

            $carts = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemleri başarıyla getirildi',
                'data' => $carts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemleri getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created cart item.
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
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'order_id' => 'nullable|exists:orders,id',
                'page_count' => 'nullable|integer',
                'notes' => 'nullable|string|json',
                'pivot_ids' => 'nullable|array',
                'pivot_ids.*' => 'integer|exists:customization_pivot_params,id',
                'order_note' => 'nullable|string|max:1000',
                'album_text' => 'nullable|string|max:500',
                'file' => 'nullable|string|url',                            // s3_zip için file linki
                'status' => 'nullable|integer',
                'discount_group_id' => 'nullable|exists:discount_groups,id',
                'urgent_status' => 'nullable|in:0,1,true,false',            // Acil üretim — frontend boolean veya 0/1
                'urgent_production' => 'nullable|boolean',                  // Alias: notes JSON'a flag olarak yazılır
                'design_service' => 'nullable|in:with_design,self_design',  // Tasarım hizmeti (notes JSON'a)
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ürünü al
            $product = Product::findOrFail($request->product_id);
            
            // Notes alanını oluştur
            $notesData = null;
            $totalCustomizationPrice = 0;
            
            // Eğer pivot_ids gönderilmişse, otomatik olarak customizations oluştur
            if ($request->has('pivot_ids') && is_array($request->pivot_ids) && count($request->pivot_ids) > 0) {
                $customizations = [];
                
                // Her pivot_id için bilgileri al
                foreach ($request->pivot_ids as $pivotId) {
                    $pivotParam = CustomizationPivotParam::with(['param.category'])
                        ->where('id', $pivotId)
                        ->where('product_id', $request->product_id) // Ürün kontrolü
                        ->first();
                    
                    if (!$pivotParam) {
                        return response()->json([
                            'success' => false,
                            'message' => "Pivot ID {$pivotId} bu ürüne ait değil veya bulunamadı"
                        ], 422);
                    }
                    
                    // Category ID'yi al (key olarak kullanılacak)
                    $categoryId = $pivotParam->customization_category_id;
                    
                    // Category type'ı al
                    $categoryType = $pivotParam->category->type ?? 'radio';
                    
                    // Customizations objesine ekle
                    // Key = category_id, value = pivot_id
                    $customizations[$categoryId] = [
                        'type' => $categoryType,
                        'value' => (string) $pivotId // pivot_id değeri
                    ];
                    
                    // Fiyatı ekle
                    $pivotPrice = $pivotParam->price;
                    // Price null, 0 veya boş string olabilir, hepsini kontrol et
                    if ($pivotPrice !== null && $pivotPrice !== '' && (is_numeric($pivotPrice) && floatval($pivotPrice) > 0)) {
                        $totalCustomizationPrice += floatval($pivotPrice);
                    }
                }
                
                // album_text varsa customization'a ekle (category_id: 8)
                if ($request->has('album_text') && !empty(trim($request->album_text))) {
                    $customizations[8] = [
                        'type' => 'radio',
                        'value' => trim($request->album_text)
                    ];
                }
                
                // Notes objesini oluştur
                $notesData = [
                    'customizations' => $customizations,
                    'total_customization_price' => number_format($totalCustomizationPrice, 2, '.', '')
                ];

                // Sipariş notu varsa ekle
                if ($request->has('order_note') && !empty(trim($request->order_note))) {
                    $notesData['order_note'] = trim($request->order_note);
                }
                // Acil üretim flag'i (notes JSON'a)
                if ($request->boolean('urgent_production')) {
                    $notesData['urgent_production'] = true;
                }
                // Tasarım hizmeti tercihi
                $designService = $request->input('design_service');
                if (in_array($designService, ['with_design', 'self_design'], true)) {
                    $notesData['design_service'] = $designService;
                }

                // JSON string'e çevir
                $request->merge(['notes' => json_encode($notesData)]);
            }
            // Eğer sadece album_text gönderilmişse (pivot_ids yoksa)
            elseif ($request->has('album_text') && !empty(trim($request->album_text))) {
                $customizations = [];
                $customizations[8] = [
                    'type' => 'radio',
                    'value' => trim($request->album_text)
                ];

                $notesData = [
                    'customizations' => $customizations,
                    'total_customization_price' => '0.00'
                ];

                if ($request->has('order_note') && !empty(trim($request->order_note))) {
                    $notesData['order_note'] = trim($request->order_note);
                }
                if ($request->boolean('urgent_production')) {
                    $notesData['urgent_production'] = true;
                }
                $designService = $request->input('design_service');
                if (in_array($designService, ['with_design', 'self_design'], true)) {
                    $notesData['design_service'] = $designService;
                }

                $request->merge(['notes' => json_encode($notesData)]);
            }
            // Eğer notes direkt gönderilmişse (eski format)
            elseif ($request->has('notes') && !empty($request->notes)) {
                // Eğer string ise JSON decode et
                if (is_string($request->notes)) {
                    $decoded = json_decode($request->notes, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Notes alanı geçerli bir JSON formatında olmalıdır',
                            'error' => json_last_error_msg()
                        ], 422);
                    }
                    $notesData = $decoded;
                } else {
                    $notesData = $request->notes;
                }
                
                // Notes formatını kontrol et
                if (is_array($notesData)) {
                    // Customizations kontrolü
                    if (isset($notesData['customizations']) && !is_array($notesData['customizations'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Notes içindeki customizations bir array olmalıdır'
                        ], 422);
                    }
                    
                    // Eğer notes'tan total_customization_price varsa, onu kullan
                    if (isset($notesData['total_customization_price'])) {
                        $totalCustomizationPrice = floatval($notesData['total_customization_price']);
                    }
                }
                
                // JSON string'e çevir
                $request->merge(['notes' => json_encode($notesData)]);
            }

            // Fiyat hesaplamaları (Frontend mantığına göre)
            $basePrice = floatval($product->price);
            $basePages = $product->min_pages ?? 10;
            $pageCount = $request->page_count ?? $basePages;
            
            // 1. Base price + Customization fiyatları
            $totalBasePrice = $basePrice + $totalCustomizationPrice;
            
            // 2. Sayfa sayısına göre fiyat hesaplama
            $pagePrice = 0;
            if ($pageCount != $basePages) {
                $priceDifferencePerPage = floatval($product->price_difference_per_page ?? 0);
                $decreasingPerPage = floatval($product->decreasing_per_page ?? 0);
                
                $pricePercentage = 0;
                if ($pageCount < $basePages) {
                    // 10 yaprak altında azalma yüzdesi kullan
                    $pricePercentage = $decreasingPerPage / 100;
                } else {
                    // 10 yaprak üstünde artış yüzdesi kullan
                    $pricePercentage = $priceDifferencePerPage / 100;
                }
                
                // Yaprak birim fiyatı hesapla
                $pricePerPage = $totalBasePrice * $pricePercentage;
                
                // Sayfa farkı
                $pageDifference = $pageCount - $basePages;
                $pagePrice = $pageDifference * $pricePerPage;
            }
            
            // 3. Toplam fiyat = Base + Customization + Page Price
            $originalPrice = $totalBasePrice + $pagePrice;
            $discountedPrice = $originalPrice;
            $discountGroupId = null;
            
            // 4. Kullanıcı bilgilerini al ve indirim hesaplaması yap
            $user = $request->user();
            
            if ($user && $user->customer_id) {
                $validDiscounts = DiscountGroup::getValidDiscountsForUser(
                    $user->id, 
                    $product->main_category_id
                );
                
                if ($validDiscounts->isNotEmpty()) {
                    $maxDiscount = $validDiscounts->max('discount_percentage');
                    $discountedPrice = $originalPrice * (1 - $maxDiscount / 100);
                    $discountGroupId = $validDiscounts->where('discount_percentage', $maxDiscount)->first()->id;
                }
            }
            
            // s3_zip alanını file'dan al
            $s3Zip = null;
            if ($request->has('file') && !empty($request->file)) {
                $s3Zip = $request->file;
            }
            
            // Cart verilerini hazırla
            $cartData = $request->all();
            $cartData['user_id'] = $userId; // Token sahibinin ID'si
            $cartData['original_price'] = $originalPrice;
            $cartData['price'] = $discountedPrice;
            
            
            if($request->has('urgent_status')){
                $cartData['urgent_status'] = 1;
            }
            
            // discount_group_id kolonu varsa ekle
            if (Schema::hasColumn('carts', 'discount_group_id')) {
                $cartData['discount_group_id'] = $discountGroupId;
            }
            
            if ($s3Zip) {
                $cartData['s3_zip'] = $s3Zip;
            }
            
            // Gereksiz alanları temizle
            if (isset($cartData['file'])) {
                unset($cartData['file']);
            }
            
            $cart = Cart::create($cartData);
            
            // Cart ID oluştur
            $cart->cart_id = $cart->generateCartIdentifier();
            
            // Barcode oluştur
            if (!$cart->barcode) {
                $cart->barcode = $cart->generateUniqueBarcode();
            }
            
            $cart->save();

            // OrderStatusHistory ekle (default: order_status_id = 1)
            OrderStatusHistory::create([
                'cart_id' => $cart->id,
                'order_status_id' => 1,
                'user_id' => $userId,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi başarıyla oluşturuldu',
                'data' => $cart->load(['user', 'product', 'order'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemi oluşturulurken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified cart item.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $cart = Cart::with([
                'user',
                'product',
                'order',
                'discountGroup',
                'statusHistories.orderStatus',
                'currentStatus.orderStatus'
            ])->findOrFail($id);

            // Status histories'yi formatla (en yeni önce)
            $statusHistories = $cart->statusHistories()
                ->with(['orderStatus'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'order_status_id' => $history->order_status_id,
                        'order_status' => $history->orderStatus ? [
                            'id' => $history->orderStatus->id,
                            'title' => $history->orderStatus->title,
                            'desc' => $history->orderStatus->desc ?? null
                        ] : null,
                        'user_id' => $history->user_id,
                        'created_at' => $history->created_at ? $history->created_at->toDateTimeString() : null
                    ];
                });

            // Current status'u formatla
            $currentStatus = null;
            if ($cart->currentStatus) {
                $currentStatus = [
                    'id' => $cart->currentStatus->id,
                    'order_status_id' => $cart->currentStatus->order_status_id,
                    'order_status' => $cart->currentStatus->orderStatus ? [
                        'id' => $cart->currentStatus->orderStatus->id,
                        'title' => $cart->currentStatus->orderStatus->title,
                        'desc' => $cart->currentStatus->orderStatus->desc ?? null
                    ] : null,
                    'user_id' => $cart->currentStatus->user_id,
                    'created_at' => $cart->currentStatus->created_at ? $cart->currentStatus->created_at->toDateTimeString() : null
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi detayları getirildi',
                'data' => [
                    'cart' => [
                        'id' => $cart->id,
                        'user_id' => $cart->user_id,
                        'product_id' => $cart->product_id,
                        'quantity' => $cart->quantity,
                        'page_count' => $cart->page_count,
                        'price' => $cart->price,
                        'original_price' => $cart->original_price,
                        'status' => $cart->status,
                        'order_id' => $cart->order_id,
                        'cart_id' => $cart->cart_id,
                        'barcode' => $cart->barcode,
                        'cargo_barcode' => $cart->cargo_barcode,
                        'tracking_url' => $cart->tracking_url,
                        's3_zip' => $cart->s3_zip,
                        'notes' => $cart->notes,
                        'parsed_notes' => $cart->parsed_notes,
                        'total_price' => $cart->total_price,
                        'thumbnail_urls' => $cart->thumbnail_urls,
                        'created_at' => $cart->created_at ? $cart->created_at->toDateTimeString() : null,
                        'updated_at' => $cart->updated_at ? $cart->updated_at->toDateTimeString() : null,
                    ],
                    'user' => $cart->user ? [
                        'id' => $cart->user->id,
                        'name' => $cart->user->name,
                        'email' => $cart->user->email
                    ] : null,
                    'product' => $cart->product ? [
                        'id' => $cart->product->id,
                        'title' => $cart->product->title,
                        'price' => $cart->product->price,
                        'slug' => $cart->product->slug
                    ] : null,
                    'order' => $cart->order ? [
                        'id' => $cart->order->id,
                        'order_number' => $cart->order->order_number,
                        'total_price' => $cart->order->total_price,
                        'status' => $cart->order->status
                    ] : null,
                    'discount_group' => $cart->discountGroup ? [
                        'id' => $cart->discountGroup->id,
                        'name' => $cart->discountGroup->name ?? null,
                        'discount_percentage' => $cart->discountGroup->discount_percentage ?? null
                    ] : null,
                    'status_histories' => $statusHistories,
                    'current_status' => $currentStatus
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemi bulunamadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified cart item.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $cart = Cart::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id' => 'sometimes|exists:users,id',
                'product_id' => 'sometimes|exists:products,id',
                'quantity' => 'sometimes|integer|min:1',
                'price' => 'sometimes|numeric|min:0',
                'order_id' => 'sometimes|exists:orders,id',
                'page_count' => 'sometimes|integer',
                'notes' => 'sometimes|string',
                'status' => 'sometimes|integer',
                'barcode' => 'sometimes|string',
                'cargo_barcode' => 'sometimes|string',
                'tracking_url' => 'sometimes|string',
                'discount_group_id' => 'sometimes|exists:discount_groups,id',
                's3_zip' => 'sometimes|nullable|string|url|max:1000',
                'urgent_status' => 'sometimes|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cart->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi başarıyla güncellendi',
                'data' => $cart->load(['user', 'product', 'order'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemi güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified cart item.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $cart = Cart::findOrFail($id);
            
            // İlişkili dosyaları sil
            $cart->deleteAssociatedFiles();
            
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi başarıyla silindi'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemi silinirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart items by order ID
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function getByOrder(int $orderId): JsonResponse
    {
        try {
            $order = Order::findOrFail($orderId);
            $cartItems = Cart::where('order_id', $orderId)
                ->with(['product', 'user', 'currentStatus'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemleri getirildi',
                'data' => [
                    'order' => $order,
                    'cart_items' => $cartItems,
                    'total_items' => $cartItems->count(),
                    'total_quantity' => $cartItems->sum('quantity'),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sipariş kalemleri getirilirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart item by barcode
     *
     * @param string $barcode
     * @return JsonResponse
     */
    public function getByBarcode(string $barcode): JsonResponse
    {
        try {
            $cart = Cart::where('barcode', $barcode)
                ->with(['user', 'product', 'order', 'currentStatus'])
                ->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barkod bulunamadı'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi bulundu',
                'data' => $cart
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barkod araması sırasında hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart status
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $cart = Cart::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cart->status = $request->status;
            $cart->save();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş kalemi durumu güncellendi',
                'data' => $cart
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Durum güncellenirken hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}





