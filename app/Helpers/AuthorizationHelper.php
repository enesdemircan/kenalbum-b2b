<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthorizationHelper
{
    /**
     * Kullanıcının belirli bir role sahip olup olmadığını kontrol et
     */
    public static function hasRole($role)
    {
        $user = Auth::user();
        return $user ? $user->hasRole($role) : false;
    }

    /**
     * Kullanıcının belirli bir permission'a sahip olup olmadığını kontrol et
     */
    public static function hasPermission($permission)
    {
        $user = Auth::user();
        return $user ? $user->hasPermission($permission) : false;
    }

    /**
     * Kullanıcının belirli bir route'a erişim izni var mı?
     */
    public static function canAccessRoute($routeName)
    {
        $user = Auth::user();
        return $user ? $user->canAccessRoute($routeName) : false;
    }

    /**
     * Kullanıcının belirli bir route'da belirli bir işlem yapabilir mi?
     */
    public static function canPerformAction($routeName, $action)
    {
        $user = Auth::user();
        return $user ? $user->canPerformAction($routeName, $action) : false;
    }

    /**
     * Kullanıcının herhangi bir role sahip olup olmadığını kontrol et
     */
    public static function hasAnyRole($roles)
    {
        $user = Auth::user();
        if (!$user) return false;

        if (is_string($roles)) {
            $roles = explode('|', $roles);
        }

        foreach ($roles as $role) {
            if ($user->hasRole(trim($role))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kullanıcının herhangi bir permission'a sahip olup olmadığını kontrol et
     */
    public static function hasAnyPermission($permissions)
    {
        $user = Auth::user();
        if (!$user) return false;

        // Administrator tüm permission'lara sahip
        if ($user->isAdministrator()) {
            return true;
        }

        if (is_string($permissions)) {
            $permissions = explode('|', $permissions);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission(trim($permission))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kullanıcının administrator olup olmadığını kontrol et
     */
    public static function isAdministrator()
    {
        $user = Auth::user();
        return $user ? $user->isAdministrator() : false;
    }

    /**
     * Kullanıcının herhangi bir route'a erişim izni var mı?
     */
    public static function canAccessAnyRoute()
    {
        $user = Auth::user();
        return $user ? $user->canAccessAnyRoute() : false;
    }
} 