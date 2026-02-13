<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'category_id',
        'category_title',
        'customization_param_id',
        'customization_pivot_params_id',
        'chunk_index',
        'total_chunks',
        'chunk_path',
        'file_order',
        'status',
        's3_path',
        'temp_cart_id'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'chunk_index' => 'integer',
        'total_chunks' => 'integer',
        'file_order' => 'string',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function category()
    {
        return $this->belongsTo(CustomizationCategory::class, 'category_id');
    }

    public function customizationParam()
    {
        return $this->belongsTo(CustomizationParam::class, 'customization_param_id');
    }

    public function customizationPivotParam()
    {
        return $this->belongsTo(CustomizationPivotParam::class, 'customization_pivot_params_id');
    }
}
