<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount_percentage',
        'main_category_id',
        'is_active',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'decimal:2'
    ];

    // Ana kategori ilişkisi
    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    // Firmalar ilişkisi (many-to-many)
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'discount_group_customer');
    }

    // Firma kullanıcıları ilişkisi (firmalar üzerinden)
    public function users()
    {
        return $this->hasManyThrough(User::class, Customer::class, 'id', 'customer_id', 'customer_id')
                    ->whereIn('customers.id', $this->customers->pluck('id'));
    }

    // Siparişler ilişkisi - kaldırıldı çünkü orders tablosunda discount_group_id sütunu yok
    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }

    // Aktif indirim gruplarını getir
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        // Başlangıç tarihi yoksa veya bugünden önceyse
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        // Bitiş tarihi yoksa veya bugünden sonraysa
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Belirli bir kullanıcı için geçerli indirim gruplarını getir
    public static function getValidDiscountsForUser($userId, $mainCategoryId)
    {
        // Önce kategori hiyerarşisini kontrol et
        $category = \App\Models\MainCategory::find($mainCategoryId);
        $parentCategoryId = $category ? $category->ust_id : null;
        
        // Kullanıcının customer_id'sini al
        $user = User::find($userId);
        $customerId = $user ? $user->customer_id : null;
        
        if (!$customerId) {
            return collect(); // Kullanıcının firması yoksa boş koleksiyon döndür
        }
        
        return self::active()
                  ->where(function($query) use ($mainCategoryId, $parentCategoryId) {
                      // Tam eşleşme veya üst kategori eşleşmesi
                      $query->where('main_category_id', $mainCategoryId)
                            ->orWhere('main_category_id', $parentCategoryId);
                  })
                  ->whereHas('customers', function($query) use ($customerId) {
                      $query->where('customers.id', $customerId);
                  })
                  ->get();
    }
}
