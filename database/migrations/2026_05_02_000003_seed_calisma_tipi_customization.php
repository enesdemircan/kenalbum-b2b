<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * "Çalışma Tipi" customization kategorisini seed eder:
 * - Kategori: title='Çalışma Tipi', type='radio', step_label='Diğer'
 * - Param'lar: 'Dizgi', 'Rötüş'
 * - Pivot: product 7 için her iki param top-level olarak bağlanır
 *
 * Diğer ürünlere de eklemek için: admin panelden manuel pivot oluştur,
 * veya bu migration'daki product_ids array'ini genişlet.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $categoryId = DB::table('customization_categories')->insertGetId([
            'ust_id' => 0,
            'title' => 'Çalışma Tipi',
            'type' => 'radio',
            'step_label' => 'Diğer',
            'description' => null,
            'required' => 0,
            'order' => 50,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // NOT: customization_params.key = frontend'de gözüken görünen isim,
        // value = (varsa) görsel path. Dizgi/Rötüş için görsel yok → value boş.
        $params = [
            ['key' => 'Dizgi', 'value' => ''],
            ['key' => 'Rötüş', 'value' => ''],
        ];

        $paramIds = [];
        foreach ($params as $i => $p) {
            $paramIds[] = DB::table('customization_params')->insertGetId([
                'ust_id' => 0,
                'customization_category_id' => $categoryId,
                'key' => $p['key'],
                'value' => $p['value'],
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $productIds = [7];
        foreach ($productIds as $productId) {
            foreach ($paramIds as $i => $paramId) {
                DB::table('customization_pivot_params')->insert([
                    'product_id' => $productId,
                    'params_id' => $paramId,
                    'customization_category_id' => $categoryId,
                    'customization_params_ust_id' => 0,
                    'is_required' => 0,
                    'order' => $i,
                    'price' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $category = DB::table('customization_categories')
            ->where('title', 'Çalışma Tipi')
            ->where('step_label', 'Diğer')
            ->first();

        if (!$category) return;

        $paramIds = DB::table('customization_params')
            ->where('customization_category_id', $category->id)
            ->pluck('id')
            ->toArray();

        DB::table('customization_pivot_params')
            ->where('customization_category_id', $category->id)
            ->delete();

        if (!empty($paramIds)) {
            DB::table('customization_params')->whereIn('id', $paramIds)->delete();
        }

        DB::table('customization_categories')->where('id', $category->id)->delete();
    }
};
