<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tüm firmaların bakiyelerini yeniden hesapla
        Customer::chunk(50, function ($customers) {
            foreach ($customers as $customer) {
                // Bu firmaya ait tüm kullanıcıları bul
                $userIds = User::where('customer_id', $customer->id)->pluck('id');
                
                if ($userIds->isEmpty()) {
                    \Log::info("Customer has no users", [
                        'customer_id' => $customer->id,
                        'firma_id' => $customer->firma_id
                    ]);
                    continue;
                }
                
                // Başlangıç bakiyesini hesapla
                // Bakiye = Tahsilatlar toplamı - Siparişler toplamı
                
                // 1. Tahsilatları topla
                $totalCollections = $customer->collections()->sum('amount');
                
                // 2. Bu kullanıcıların tüm siparişlerini al (sadece iptal EDİLMEMİŞ)
                $orders = Order::whereIn('user_id', $userIds)
                    ->where('status', '!=', 3) // Status 3 = İptal (iptal edilenler bakiyeden düşülmemeli)
                    ->get();
                
                // Toplam sipariş tutarını hesapla
                $totalSpent = 0;
                foreach ($orders as $order) {
                    $totalSpent += $order->total_price;
                }
                
                // Yeni bakiye = Tahsilatlar - Harcanan
                $newBalance = $totalCollections - $totalSpent;
                
                \Log::info("Customer balance recalculated", [
                    'customer_id' => $customer->id,
                    'firma_id' => $customer->firma_id,
                    'unvan' => $customer->unvan,
                    'old_balance' => $customer->balance,
                    'total_collections' => $totalCollections,
                    'total_spent' => $totalSpent,
                    'new_balance' => $newBalance,
                    'order_count' => $orders->count(),
                    'user_count' => $userIds->count(),
                    'difference' => $newBalance - $customer->balance
                ]);
                
                // Bakiyeyi güncelle
                $customer->update(['balance' => $newBalance]);
            }
        });
        
        \Log::info("✅ Customer balance fix completed!");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Log::warning('Cannot reverse customer balance fix migration');
    }
};
