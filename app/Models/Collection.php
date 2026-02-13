<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'amount',
        'payment_method',
        'collection_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collection_date' => 'date',
    ];

    // Customer ile ilişki
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Tahsilat şekli metinleri
    public function getPaymentMethodTextAttribute()
    {
        return match($this->payment_method) {
            'kredi_karti' => 'Kredi Kartı',
            'havale' => 'Havale',
            'nakit' => 'Nakit',
            default => 'Bilinmiyor'
        };
    }

    // Tahsilat şekli badge rengi
    public function getPaymentMethodBadgeClassAttribute()
    {
        return match($this->payment_method) {
            'kredi_karti' => 'bg-primary',
            'havale' => 'bg-info',
            'nakit' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    // Formatlanmış miktar
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2, ',', '.') . ' ₺';
    }

    // Formatlanmış tarih
    public function getFormattedDateAttribute()
    {
        return $this->collection_date->format('d.m.Y');
    }
} 