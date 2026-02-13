<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Sadece status değişikliklerini kontrol et
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            Log::info('Order status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'total_price' => $order->total_price
            ]);
            
            // Kullanıcıyı ve customer'ı bul
            $user = $order->user;
            if (!$user || !$user->customer) {
                Log::warning('Order user or customer not found', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id
                ]);
                return;
            }
            
            $customer = $user->customer;
            
            // Sipariş toplam tutarını hesapla (cart items'dan)
            $totalPrice = 0;
            foreach ($order->cartItems as $cartItem) {
                $totalPrice += $cartItem->price * $cartItem->quantity;
            }
            
            // Eğer cart items boşsa order total_price kullan
            if ($totalPrice == 0) {
                $totalPrice = $order->total_price;
            }
            
            // Status 3 = İptal
            // Sipariş iptal edildiyse bakiyeyi iade et
            if ($newStatus == 3 && $oldStatus != 3) {
                // Bakiyeyi geri ekle
                $customer->addBalance($totalPrice);
                
                Log::info('Order cancelled, balance refunded', [
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'refund_amount' => $totalPrice,
                    'new_balance' => $customer->balance
                ]);
            }
            
            // İptalden başka bir duruma geçtiyse bakiyeyi tekrar düş
            elseif ($oldStatus == 3 && $newStatus != 3) {
                // Bakiyeyi tekrar düş
                $customer->subtractBalance($totalPrice);
                
                Log::info('Order reactivated, balance deducted', [
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'deducted_amount' => $totalPrice,
                    'new_balance' => $customer->balance
                ]);
            }
        }
    }
}
