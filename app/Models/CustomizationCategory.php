<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomizationCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ust_id', 'title', 'type', 'step_label', 'required', 'order', 'option1', 'option2'
    ];

    public function params()
    {
        return $this->hasMany(CustomizationParam::class, 'customization_category_id');
    }

    public function parent()
    {
        return $this->belongsTo(CustomizationCategory::class, 'ust_id');
    }

    public function children()
    {
        return $this->hasMany(CustomizationCategory::class, 'ust_id');
    }
}
