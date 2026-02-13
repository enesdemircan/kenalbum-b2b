<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'ust_id', 'title', 'slug', 'order', 'option1', 'option2'
    ];

    public function parent()
    {
        return $this->belongsTo(MainCategory::class, 'ust_id');
    }

    public function children()
    {
        return $this->hasMany(MainCategory::class, 'ust_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'main_category_id');
    }
}
