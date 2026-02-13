<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizationParam extends Model
{
    use HasFactory;

    protected $table = 'customization_params';
    
    protected $fillable = [
        'ust_id',
        'customization_category_id',
        'key',
        'value',
        'order',
        'option1',
        'option2'
    ];

    public function category()
    {
        return $this->belongsTo(CustomizationCategory::class, 'customization_category_id');
    }

    public function parent()
    {
        return $this->belongsTo(CustomizationParam::class, 'ust_id');
    }

    public function children()
    {
        return $this->hasMany(CustomizationParam::class, 'ust_id');
    }
}
