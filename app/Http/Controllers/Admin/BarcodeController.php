<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    public function search(Request $request)
    {
        $barcode = $request->get('barcode');
        $message = '';
        $messageType = '';
        
        if ($barcode) {
            // Cart ID ile cart'ı ara (özel format)
            $cart = Cart::with([
                'order',
                'user.customer',
                'product'
            ])->where('cart_id', 'like', '%' . $barcode . '%')->first();
            
            // Eğer cart_id ile bulunamazsa, barcode ile de ara
            if (!$cart) {
                $cart = Cart::with([
                    'order',
                    'user.customer',
                    'product'
                ])->where('barcode', 'like', '%' . $barcode . '%')->first();
            }
            
            if ($cart) {
                // Cart'ı otomatik olarak listeye ekle
                $cartList = session('cart_list', []);
                
                // Cart zaten listede var mı kontrol et
                if (!in_array($cart->id, array_column($cartList, 'id'))) {
                    $cartList[] = [
                        'id' => $cart->id,
                        'cart_id' => $cart->cart_id,
                        'barcode' => $cart->barcode,
                        'order_number' => $cart->order->order_number ?? 'N/A',
                        'customer_name' => ($cart->user->customer->name ?? '') . ' ' . ($cart->user->customer->surname ?? ''),
                        'company_name' => $cart->user->customer->unvan ?? '',
                        'product_title' => $cart->product->title ?? '',
                        'quantity' => $cart->quantity,
                        'page_count' => $cart->page_count,
                        'current_status' => $this->getCurrentStatus($cart)
                    ];
                    
                    session(['cart_list' => $cartList]);
                    $message = 'Sipariş bulundu ve listeye eklendi!';
                    $messageType = 'success';
                } else {
                    $message = 'Bu Sipariş zaten listede bulunuyor.';
                    $messageType = 'warning';
                }
            } else {
                $message = 'Bu barcode ile eşleşen sipariş bulunamadı.';
                $messageType = 'danger';
            }
        }
        
        // Session'dan cart listesini al
        $cartList = session('cart_list', []);
        
        // Kullanıcının rolüne göre kullanabileceği durumları getir
        $user = Auth::user();
        $orderStatuses = OrderStatus::query();
        
        // Administrator tüm durumları görebilir
        $isAdministrator = $user->roles()->whereIn('name', ['administrator', 'Administrator'])->exists();
        
        if (!$isAdministrator) {
            // Kullanıcının rollerine göre kontrol et
            $userRoleIds = $user->roles()->pluck('roles.id');
            $orderStatuses = $orderStatuses->whereHas('roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            });
        }
        
        $orderStatuses = $orderStatuses->get();
        
        // AJAX isteği ise JSON döndür
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'messageType' => $messageType,
                'cartList' => $cartList
            ]);
        }
        
        return view('admin.barcode-search', compact('message', 'messageType', 'cartList', 'orderStatuses'));
    }

    public function removeFromCartList(Request $request)
    {
        $cartId = $request->get('cart_id');
        $cartList = session('cart_list', []);
        
        // Cart'ı listeden kaldır
        $cartList = array_filter($cartList, function($item) use ($cartId) {
            return $item['id'] != $cartId;
        });
        
        session(['cart_list' => array_values($cartList)]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Cart listeden kaldırıldı.',
            'cartList' => $cartList
        ]);
    }

    public function clearCartList()
    {
        session()->forget('cart_list');
        
        return response()->json([
            'success' => true, 
            'message' => 'Cart listesi temizlendi.'
        ]);
    }

    public function updateCartStatuses(Request $request)
    {
        $request->validate([
            'cart_ids' => 'required|array',
            'cart_ids.*' => 'exists:carts,id',
            'order_status_id' => 'required|exists:order_statuses,id'
        ]);
        
        $cartIds = $request->get('cart_ids');
        $orderStatusId = $request->get('order_status_id');
        $userId = Auth::id();
        
        // Debug log
        \Log::info('BarcodeController updateCartStatuses', [
            'cart_ids' => $cartIds,
            'order_status_id' => $orderStatusId,
            'user_id' => $userId
        ]);
        
        $updatedCount = 0;
        
        foreach ($cartIds as $cartId) {
            $cart = Cart::find($cartId);
            \Log::info('Processing cart', [
                'cart_id' => $cartId,
                'cart_found' => $cart ? 'yes' : 'no'
            ]);
            
            if ($cart) {
                try {
                    // OrderStatusHistory oluştur - cart_id alanına cart ID yazılıyor
                    $history = OrderStatusHistory::create([
                        'cart_id' => $cartId, // Cart ID'yi cart_id alanına yaz
                        'order_status_id' => $orderStatusId,
                        'user_id' => $userId
                    ]);
                    
                    \Log::info('OrderStatusHistory created', [
                        'history_id' => $history->id,
                        'cart_id' => $history->cart_id, // Bu artık gerçek cart_id
                        'status_id' => $history->order_status_id
                    ]);
                    
                    $updatedCount++;
                } catch (\Exception $e) {
                    \Log::error('Error creating OrderStatusHistory', [
                        'error' => $e->getMessage(),
                        'cart_id' => $cartId
                    ]);
                }
            }
        }
        
        if ($updatedCount > 0) {
            // Güncellenen cart'ları listeden kaldır
            $cartList = session('cart_list', []);
            $cartList = array_filter($cartList, function($item) use ($cartIds) {
                return !in_array($item['id'], $cartIds);
            });
            session(['cart_list' => array_values($cartList)]);
            
            return response()->json([
                'success' => true, 
                'message' => "{$updatedCount} cart'ın durumu güncellendi.",
                'cartList' => $cartList
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Güncelleme yapılamadı.']);
    }

    private function getCurrentStatus($cart)
    {
        // Cart'ın mevcut durumunu bul - cart_id alanında cart ID saklanıyor
        $lastHistory = OrderStatusHistory::where('cart_id', $cart->id)
            ->latest('created_at')
            ->first();
            
        if ($lastHistory) {
            return $lastHistory->orderStatus->title ?? 'Bilinmiyor';
        }
        
        return 'Yeni Sipariş';
    }
} 