<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Cart;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tüm siparişleri kontrol et ve toplam fiyatı düzelt
        Order::with('cartItems')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $correctTotalPrice = 0;
                
                foreach ($order->cartItems as $cartItem) {
                    // Her cart item için toplam fiyat = fiyat * adet
                    $correctTotalPrice += $cartItem->price * $cartItem->quantity;
                }
                
                // Eğer siparişteki toplam fiyat yanlışsa düzelt
                if ($order->total_price != $correctTotalPrice) {
                    \Log::info("Fixing order total price", [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'old_total' => $order->total_price,
                        'new_total' => $correctTotalPrice,
                        'cart_items_count' => $order->cartItems->count()
                    ]);
                    
                    $order->update(['total_price' => $correctTotalPrice]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bu migration geri alınamaz çünkü eski yanlış değerleri bilmiyoruz
        \Log::warning('Cannot reverse order total price fix migration');
    }
};
