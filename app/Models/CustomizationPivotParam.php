<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomizationPivotParam extends Model
{
    use HasFactory;
    
    protected $table = 'customization_pivot_params';
    
    protected $fillable = [
        'product_id',
        'params_id',
        'price',
        'option1',
        'option2',
        'customization_category_id',
        'customization_params_ust_id',
        'order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function param()
    {
        return $this->belongsTo(CustomizationParam::class, 'params_id');
    }

    public function category()
    {
        return $this->belongsTo(CustomizationCategory::class, 'customization_category_id');
    }
    // Bu pivot kaydının child'ları var mı kontrol et
    public function hasChildren()
    {
        return \App\Models\CustomizationParam::where('ust_id', $this->params_id)->exists();
    }

    // Bu pivot kaydının child'larını getir (tek seviye)
    public function getChildren()
    {
        // Bu pivot parametresinin child'larını al
        // customization_params_ust_id = bu pivot'un id'si olan kayıtlar
        return CustomizationPivotParam::where('customization_params_ust_id', $this->id)->get();
    }
    
    // Bu pivot kaydının tüm child'larını recursive olarak getir
    public function getAllChildren()
    {
        $children = $this->getChildren();
        $allChildren = collect($children);
        
        foreach ($children as $child) {
            $allChildren = $allChildren->merge($child->getAllChildren());
        }
        
        return $allChildren;
    }
    
    public function isMainParam()
    {
        // Bu pivot parametresi ana parametre mi? (customization_params_ust_id = 0)
        return $this->customization_params_ust_id == 0;
    }
   

    // Bu pivot kaydının parent'ı var mı kontrol et
    public function hasParent()
    {
        return $this->customization_params_ust_id > 0;
    }

    // Bu pivot kaydının parent'ını getir
    public function getParent()
    {
        if ($this->hasParent()) {
            return static::where('id', $this->customization_params_ust_id)->first();
        }
        return null;
    }

    // Tüm child kayıtlarını say
    public function getChildrenCount()
    {
        return \App\Models\CustomizationParam::where('ust_id', $this->params_id)->count();
    }
}
