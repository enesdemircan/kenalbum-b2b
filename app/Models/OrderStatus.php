<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'desc'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'order_status_role')
                    ->withTimestamps();
    }

    public function orderStatusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderStatusHistory::class);
    }
} 