<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Mevcut step_label seed'ini normalize et:
 * - Her ana customization kategorisi kendi adıyla bir tab olsun (Ebat, Kumaş, Renk, Paket vb.)
 * - "Albüm Üzerine Yazılacak Yazı" + "Not" tek "Diğer" tab'ında gruplansın
 *   (admin yeni "Çalışma Tipi", "Dizgi", "Rötüş" gibi kategorileri de step_label='Diğer' ile aynı tab'a alabilir)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Her kategori kendi başlığıyla → ayrı tab
        $rules = [
            ['title_like' => 'Ebat',       'label' => 'Ebat'],
            ['title_like' => 'Paket',      'label' => 'Paket'],
            ['title_like' => 'Kumaş',      'label' => 'Kumaş'],
            ['title_like' => 'Kumas',      'label' => 'Kumaş'],
            ['title_like' => 'Renk',       'label' => 'Renk'],
            ['title_like' => 'Model',      'label' => 'Model'],
            ['title_like' => 'Boyut',      'label' => 'Ebat'],
            ['title_like' => 'Pvc Kalın',  'label' => 'Diğer'],
            // "Diğer" tab — birden fazla kategori bu label ile gruplanır
            ['title_like' => 'Albüm Üzerine', 'label' => 'Diğer'],
            ['title_like' => 'Yazılacak Yazı','label' => 'Diğer'],
            ['title_like' => 'Not',          'label' => 'Diğer'],
            ['title_like' => 'Extra Ürün',   'label' => 'Diğer'],
            // Yeni kategoriler eklenecek (Çalışma Tipi, Dizgi, Rötüş vb.) — bunlar da admin
            // panelden step_label='Diğer' set ederek aynı tab'da görünür hale gelir.
        ];

        foreach ($rules as $rule) {
            DB::statement(
                "UPDATE customization_categories
                    SET step_label = ?
                    WHERE type NOT IN ('file', 'files')
                      AND title LIKE ?",
                [$rule['label'], '%' . $rule['title_like'] . '%']
            );
        }
    }

    public function down(): void
    {
        // Eski seed'e geri dönüş yok — admin panelden manuel düzenleyebilir
    }
};
