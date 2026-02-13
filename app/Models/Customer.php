<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'firma_id',
        'unvan',
        'phone',
        'email',
        'adres',
        'vergi_dairesi',
        'vergi_numarasi',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Users ile ilişki
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Tahsilatlar ile ilişki
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    // Toplam tahsilat miktarı
    public function getTotalCollectionsAttribute()
    {
        return $this->collections()->sum('amount');
    }

    // Formatlanmış toplam tahsilat
    public function getFormattedTotalCollectionsAttribute()
    {
        return number_format($this->total_collections, 2, ',', '.') . ' ₺';
    }

    /**
     * Bakiye ekleme
     */
    public function addBalance($amount)
    {
        $this->increment('balance', $amount);
        return $this->fresh();
    }

    /**
     * Bakiye çıkarma
     */
    public function subtractBalance($amount)
    {
        $this->decrement('balance', $amount);
        return $this->fresh();
    }

    /**
     * Bakiye güncelleme
     */
    public function updateBalance($amount)
    {
        $this->update(['balance' => $amount]);
        return $this->fresh();
    }

    /**
     * Bakiye formatı
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2, ',', '.') . ' ₺';
    }

    /**
     * Bakiye durumu
     */
    public function getBalanceStatusAttribute()
    {
        if ($this->balance > 0) {
            return 'positive';
        } elseif ($this->balance < 0) {
            return 'negative';
        } else {
            return 'zero';
        }
    }

    /**
     * Bakiye yeterli mi kontrol et
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }
}
