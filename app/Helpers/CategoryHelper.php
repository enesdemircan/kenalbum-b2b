<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryHelper
{
    /**
     * İçinde aktif (status=1) ana ürün (ust_id IS NULL) bulunan kategori ID'leri.
     * Üst kategoriler altındaki alt kategorilerde ürün varsa onlar da listeye girer
     * — view'lar bu listeyi 'whereIn' filtresi olarak kullanıp ürünsüz kategorileri
     * tüm site genelinde gizler (header nav, sub-menüler, hızlı sipariş modal).
     *
     * Cache 5 dk — ürün/kategori değişiklikleri max gecikme ile yansır.
     *
     * @return int[] kategori ID listesi
     */
    public static function idsWithProducts(): array
    {
        return Cache::remember('category_ids_with_products', 300, function () {
            // Doğrudan ürünü olan kategoriler (ana ürünler, status=1)
            $direct = DB::table('products')
                ->where('status', 1)
                ->whereNull('ust_id')
                ->whereNotNull('main_category_id')
                ->distinct()
                ->pluck('main_category_id')
                ->all();

            if (empty($direct)) return [];

            // Bu kategorilerin üst (parent) ID'leri — hierarchy'de görünür kalsın diye
            $parents = DB::table('main_categories')
                ->whereIn('id', $direct)
                ->where('ust_id', '!=', 0)
                ->pluck('ust_id')
                ->all();

            return array_values(array_unique(array_merge($direct, $parents)));
        });
    }

    /**
     * Cache'i temizle (kategori veya ürün CRUD'larından sonra çağrılabilir).
     */
    public static function flushCache(): void
    {
        Cache::forget('category_ids_with_products');
    }
}
