<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\CustomizationCategory;
use App\Models\CustomizationPivotParam;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 'slug', 'price', 'urgent_price', 'main_category_id', 'images', 'thumbnails', 'template_url', 'status', 'order', 'price_difference_per_page', 'decreasing_per_page', 'max_pages', 'min_pages', 'option1', 'option2', 'suggested_products', 'ust_id', 'tags', 'stock_status', 'description'
    ];

    protected $casts = [
        'suggested_products' => 'array',
    ];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
    
    // Üst ürün ilişkisi
    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'ust_id');
    }
    
    // Alt ürünler ilişkisi
    public function childProducts()
    {
        return $this->hasMany(Product::class, 'ust_id');
    }
    
    // Ana ürün mü kontrolü
    public function isMainProduct()
    {
        return $this->ust_id === null || $this->ust_id === 0;
    }
    
    // Alt ürün mü kontrolü
    public function isChildProduct()
    {
        return $this->ust_id !== null && $this->ust_id !== 0;
    }
    
    public function getCustomizationCategories()
    {
        $customizationPivotParams = CustomizationPivotParam::where('product_id', $this->id)->get();
        $customizationCategoryIds = $customizationPivotParams->pluck('customization_category_id')->unique();
        
        return CustomizationCategory::whereIn('id', $customizationCategoryIds)->get();
    }
    
    public function customizationPivotParams()
    {
        return $this->hasMany(CustomizationPivotParam::class);
    }
    
    public function getSuggestedProducts()
    {
        if (!$this->suggested_products) {
            return collect();
        }
        
        return Product::whereIn('id', $this->suggested_products)
            ->where('status', 1)
            ->where('main_category_id', '!=', 1) // 1 numaralı kategori hariç
            ->get();
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    // Ana ürün olarak extra sales ilişkisi
    public function extraSales()
    {
        return $this->hasMany(ExtraSale::class, 'main_product_id');
    }

    // Child ürün olarak extra sales ilişkisi
    public function extraSalesAsChild()
    {
        return $this->hasMany(ExtraSale::class, 'child_product_id');
    }

    // Aktif extra sales ürünlerini getir
    public function getActiveExtraSales()
    {
        return $this->extraSales()
            ->where('is_active', true)
            ->with('childProduct')
            ->orderBy('sort_order')
            ->get()
            ->pluck('childProduct')
            ->filter();
    }
}
