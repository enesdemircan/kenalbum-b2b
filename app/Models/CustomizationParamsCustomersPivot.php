<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomizationParamsCustomersPivot extends Model
{
    use HasFactory;

    protected $table = 'customization_params_customers_pivot';

    protected $fillable = [
        'customer_id',
        'customization_params_id',
        'product_id'
    ];

    /**
     * Get the customer that owns the pivot record
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the customization param that owns the pivot record
     */
    public function customizationParam()
    {
        return $this->belongsTo(CustomizationParam::class, 'customization_params_id');
    }

    /**
     * Get the product that owns the pivot record
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
