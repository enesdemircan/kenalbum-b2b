<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'customer_id',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_numeric($role)) {
            return $this->roles->contains('id', $role);
        }
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        if ($role instanceof \Illuminate\Support\Collection) {
            return $this->roles->intersect($role)->count() > 0;
        }
        if (is_array($role)) {
            return $this->roles->whereIn('name', $role)->count() > 0;
        }
        return false;
    }

    /**
     * Kullanıcının belirli bir permission'a sahip olup olmadığını kontrol et
     */
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kullanıcının belirli bir route'a erişim izni var mı?
     */
    public function canAccessRoute($routeName)
    {
        foreach ($this->roles as $role) {
            if ($role->hasRouteAccess($routeName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kullanıcının belirli bir route'da belirli bir işlem yapabilir mi?
     */
    public function canPerformAction($routeName, $action)
    {
        foreach ($this->roles as $role) {
            if ($role->canPerformAction($routeName, $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kullanıcının tüm izinlerini getir
     */
    public function getAllPermissions()
    {
        $permissions = [];
        
        foreach ($this->roles as $role) {
            $rolePermissions = $role->getAllPermissions();
            $permissions = array_merge($permissions, $rolePermissions);
        }
        
        return $permissions;
    }

    /**
     * Kullanıcının administrator olup olmadığını kontrol et
     */
    public function isAdministrator()
    {
        foreach ($this->roles as $role) {
            if ($role->name === 'administrator' || $role->name === 'Administrator') {
                return true;
            }
        }
        return false;
    }

    /**
     * Kullanıcının herhangi bir route'a erişim izni var mı? (Administrator için otomatik izin)
     */
    public function canAccessAnyRoute()
    {
        // Administrator tüm route'lara erişebilir
        if ($this->isAdministrator()) {
            return true;
        }

        // Diğer roller için normal kontrol
        foreach ($this->roles as $role) {
            if ($role->routes()->exists()) {
                return true;
            }
        }
        return false;
    }

    // İndirim grupları ilişkisi
    public function discountGroups()
    {
        return $this->belongsToMany(DiscountGroup::class, 'discount_group_user');
    }

    // Siparişler ilişkisi
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Sepet ilişkisi
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    // Adresler ilişkisi
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    // Customer ilişkisi
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Kullanıcının onaylanmış olup olmadığını kontrol et
     */
    public function isApproved()
    {
        return $this->status == 1;
    }

    /**
     * Kullanıcının onay bekleyip beklemediğini kontrol et
     */
    public function isPending()
    {
        return $this->status == 0;
    }

    /**
     * Status açıklamasını getir
     */
    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'Onaylandı' : 'Onay Bekliyor';
    }

    /**
     * Status badge rengini getir
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status == 1 ? 'success' : 'warning';
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
