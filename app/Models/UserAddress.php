<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',           // 'company' (Şirket — Bana Gelsin) | 'customer' (Müşteri — Müşterime Gitsin)
        'title',
        'ad',
        'soyad',
        'adres',
        'telefon',
        'city',
        'district',
    ];

    public function scopeCompany($query) { return $query->where('type', 'company'); }
    public function scopeCustomer($query) { return $query->where('type', 'customer'); }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
