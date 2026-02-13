<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'uri',
        'method',
        'group',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Bu route'a sahip rolleri getir
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_route')
                    ->withTimestamps();
    }

    /**
     * Aktif route'ları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Belirli bir gruba ait route'ları getir
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Belirli bir HTTP method'una ait route'ları getir
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * Route'un tam URL'ini oluştur
     */
    public function getFullUrlAttribute()
    {
        return url($this->uri);
    }

    /**
     * Route'un açıklamasını getir
     */
    public function getDisplayNameAttribute()
    {
        return $this->description ?: $this->name;
    }
}
