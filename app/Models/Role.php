<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function orderStatuses()
    {
        return $this->belongsToMany(OrderStatus::class, 'order_status_role')
                    ->withTimestamps();
    }

    /**
     * Bu role'e ait route'ları getir
     */
    public function routes()
    {
        return $this->belongsToMany(Route::class, 'role_route')
                    ->withTimestamps();
    }

    /**
     * Belirli bir route'a erişim izni var mı?
     */
    public function hasRouteAccess($routeName)
    {
        return $this->routes()->where('name', $routeName)->exists();
    }

    /**
     * Belirli bir route'da belirli bir işlem yapabilir mi?
     */
    public function canPerformAction($routeName, $action)
    {
        // Artık sadece route'a erişim varsa tüm işlemleri yapabilir
        return $this->hasRouteAccess($routeName);
    }

    /**
     * Role'ün tüm izinlerini getir
     */
    public function getAllPermissions()
    {
        $permissions = [];
        
        foreach ($this->routes as $route) {
            $permissions[$route->name] = [
                'access' => true,
                'create' => true,
                'read' => true,
                'update' => true,
                'delete' => true,
            ];
        }
        
        return $permissions;
    }

    /**
     * Role'e route izni ata
     */
    public function assignRoute($routeId, $permissions = [])
    {
        $this->routes()->syncWithoutDetaching([$routeId]);
    }

    /**
     * Role'den route iznini kaldır
     */
    public function removeRoute($routeId)
    {
        $this->routes()->detach($routeId);
    }

    /**
     * Role'ün permission'ları
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Role'ün belirli bir permission'a sahip olup olmadığını kontrol et
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }
        if (is_numeric($permission)) {
            return $this->permissions()->where('id', $permission)->exists();
        }
        return false;
    }

    /**
     * Role'e permission ata
     */
    public function assignPermission($permissionId)
    {
        $this->permissions()->syncWithoutDetaching([$permissionId]);
    }

    /**
     * Role'den permission kaldır
     */
    public function removePermission($permissionId)
    {
        $this->permissions()->detach($permissionId);
    }

    /**
     * Role'ün tüm permission'larını getir
     */
    public function getAllPermissionSlugs()
    {
        return $this->permissions()->pluck('slug')->toArray();
    }
}
