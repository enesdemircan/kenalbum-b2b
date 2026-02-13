<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'customer_name', 'customer_surname', 'customer_phone',
        'city', 'district', 'shipping_address', 'payment_method', 'notes', 'total_price', 'discount_amount', 'status'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sipariş kalemleri (carts tablosundan)
    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'order_id');
    }

    // Sipariş durum geçmişi - accessor metodu olarak
    public function getOrderStatusHistoriesAttribute()
    {
        // Bu order'a ait cart'ların ID'lerini al
        $cartIds = $this->cartItems()->pluck('id');
        
        // Bu cart ID'lere ait OrderStatusHistory'leri getir
        return OrderStatusHistory::whereIn('cart_id', $cartIds)->get();
    }

    // Sipariş kalemlerinin durumları - removed since orderStatus relationship was removed
    // public function getCartItemStatusesAttribute() { ... }

    // Siparişin genel durumu - removed since orderStatus relationship was removed  
    // public function getOverallStatusAttribute() { ... }

    // Tüm sipariş durumları
    public function orderStatuses()
    {
        // Bu order'a ait cart'ların ID'lerini al
        $cartIds = $this->cartItems()->pluck('id');
        
        // Bu cart ID'lere ait OrderStatus'leri getir
        return OrderStatus::whereHas('orderStatusHistories', function($query) use ($cartIds) {
            $query->whereIn('cart_id', $cartIds);
        })->get();
    }

    // Status helper metodları
    public function isPending()
    {
        return $this->status === 0;
    }

    public function isProcessing()
    {
        return $this->status === 1;
    }

    public function isDelivered()
    {
        return $this->status === 2;
    }

    public function isCancelled()
    {
        return $this->status === 3;
    }

    public function getStatusTextAttribute()
    {
        switch($this->status) {
            case 0:
                return 'Onay Bekliyor';
            case 1:
                return 'İşlemde';
            case 2:
                return 'Teslim Edildi';
            case 3:
                return 'İptal';
            default:
                return 'Bilinmiyor';
        }
    }

    public function getStatusBadgeClassAttribute()
    {
        switch($this->status) {
            case 0:
                return 'bg-info'; // Onay Bekliyor - mavi
            case 1:
                return 'bg-warning'; // İşlemde - sarı
            case 2:
                return 'bg-success'; // Teslim Edildi - yeşil
            case 3:
                return 'bg-danger'; // İptal - kırmızı
            default:
                return 'bg-secondary';
        }
    }

    public function latestStatusHistory()
    {
        // Bu order'a ait cart'ların ID'lerini al
        $cartIds = $this->cartItems()->pluck('id');
        
        // En son OrderStatusHistory'yi getir
        return OrderStatusHistory::whereIn('cart_id', $cartIds)->orderBy('created_at', 'desc')->first();
    }
    
    public function statusHistories()
    {
        // Bu order'a ait cart'ların ID'lerini al
        $cartIds = $this->cartItems()->pluck('id');
        
        // Bu cart ID'lere ait OrderStatusHistory'leri getir
        return OrderStatusHistory::whereIn('cart_id', $cartIds)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Sipariş durumu "işlemde" olduğunda bakiyeden toplam ücreti düşür
     */
    public function processPayment()
    {
        // Sadece durum "işlemde" (1) olduğunda işlem yap
        if ($this->status !== 1) {
            \Log::info('Order status is not 1, skipping payment processing', [
                'order_id' => $this->id,
                'status' => $this->status
            ]);
            return false;
        }

        // Kullanıcının firmasını bul
        $customer = $this->user->customer;
        if (!$customer) {
            \Log::error('Customer not found for user', [
                'order_id' => $this->id,
                'user_id' => $this->user_id,
                'user_customer_id' => $this->user->customer_id ?? 'null'
            ]);
            return false;
        }

        \Log::info('Customer found for payment processing', [
            'order_id' => $this->id,
            'customer_id' => $customer->id,
            'customer_unvan' => $customer->unvan,
            'current_balance' => $customer->balance,
            'amount_to_deduct' => $this->total_price
        ]);

        // Bakiye kontrolü kaldırıldı - hesapta para olacak
        \Log::info('Balance check skipped - proceeding with payment', [
            'order_id' => $this->id,
            'customer_id' => $customer->id,
            'current_balance' => $customer->balance,
            'amount_to_deduct' => $this->total_price
        ]);

        // Bakiyeden düş
        $oldBalance = $customer->balance;
        $customer->subtractBalance($this->total_price);
        
        \Log::info('Payment processed successfully', [
            'order_id' => $this->id,
            'customer_id' => $customer->id,
            'old_balance' => $oldBalance,
            'new_balance' => $customer->balance,
            'amount_deducted' => $this->total_price
        ]);
        
        return true;
    }

    /**
     * Sipariş iptal edildiğinde bakiyeyi geri ekle
     */
    public function refundPayment()
    {
        // Sadece durum "iptal" (3) olduğunda işlem yap
        if ($this->status !== 3) {
            return false;
        }

        // Kullanıcının firmasını bul
        $customer = $this->user->customer;
        if (!$customer) {
            return false;
        }

        // Bakiyeyi geri ekle
        $customer->addBalance($this->total_price);
        
        return true;
    }
}
