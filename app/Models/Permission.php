<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Permission slug'ını oluştur
     */
    public static function createSlug($name)
    {
        return strtolower(str_replace([' ', '-'], '_', $name));
    }

    /**
     * Belirli bir modüle ait permission'ları getir
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }
} 