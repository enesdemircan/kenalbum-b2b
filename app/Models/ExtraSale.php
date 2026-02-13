<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_product_id',
        'child_product_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Ana ürün ilişkisi
    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id');
    }

    // Child ürün ilişkisi
    public function childProduct()
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }
}
