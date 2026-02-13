<?php

namespace App\Helpers;

class RoutePermissionHelper
{
    /**
     * Route'un izin türlerini belirle
     */
    public static function getPermissionTypesForRoute($routeName, $method)
    {
        // Sadece erişim izni
        return ['can_access'];
    }

    /**
     * Route'un açıklamasını getir
     */
    public static function getRouteDescription($routeName, $method)
    {
        $descriptions = [
            'index' => 'Listeleme',
            'create' => 'Oluşturma Formu',
            'store' => 'Kaydetme',
            'show' => 'Detay Görüntüleme',
            'edit' => 'Düzenleme Formu',
            'update' => 'Güncelleme',
            'destroy' => 'Silme',
            'delete' => 'Silme',
            'import' => 'İçe Aktarma',
            'export' => 'Dışa Aktarma',
            'toggle' => 'Durum Değiştirme',
            'assign' => 'Atama',
            'remove' => 'Kaldırma',
            'upload' => 'Dosya Yükleme',
            'download' => 'Dosya İndirme',
        ];

        foreach ($descriptions as $action => $description) {
            if (str_contains($routeName, ".{$action}")) {
                return $description;
            }
        }

        // HTTP method'a göre varsayılan açıklama
        switch (strtoupper($method)) {
            case 'GET':
                return 'Görüntüleme';
            case 'POST':
                return 'Kaydetme';
            case 'PUT':
            case 'PATCH':
                return 'Güncelleme';
            case 'DELETE':
                return 'Silme';
            default:
                return 'İşlem';
        }
    }

    /**
     * Route'un izin açıklamalarını getir
     */
    public static function getPermissionDescriptions($routeName, $method)
    {
        return ['can_access' => 'Erişim'];
    }

    /**
     * Route'un önem derecesini belirle
     */
    public static function getRouteImportance($routeName)
    {
        $criticalRoutes = [
            'admin.dashboard',
            'admin.users',
            'admin.roles',
            'admin.routes',
            'admin.site-settings'
        ];

        $importantRoutes = [
            'admin.products',
            'admin.orders',
            'admin.customers',
            'admin.bank-accounts'
        ];

        if (in_array($routeName, $criticalRoutes)) {
            return 'critical';
        } elseif (in_array($routeName, $importantRoutes)) {
            return 'important';
        } else {
            return 'normal';
        }
    }
} 