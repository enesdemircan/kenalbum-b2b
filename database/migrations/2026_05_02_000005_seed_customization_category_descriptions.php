<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tüm customization kategorilerine standart bir açıklama (description)
 * tanımlar — sipariş wizard tab'larında title altında gözüken yardımcı
 * metin. Sadece description'ı NULL olan satırlara yazılır; admin daha
 * sonra panelden override edebilir.
 *
 * Title LIKE matching kullanılır — kategori ID'leri ortama göre değişebilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rules = [
            ['title_like' => 'Ebat',           'desc' => 'Albümünüzün ebatını seçin.'],
            ['title_like' => 'Kumaş',          'desc' => 'Albüm kapağı için kumaş tipini seçin.'],
            ['title_like' => 'Kumas',          'desc' => 'Albüm kapağı için kumaş tipini seçin.'],
            ['title_like' => 'Renk',           'desc' => 'Albüm kapağının renk veya desenini seçin.'],
            ['title_like' => 'Model',          'desc' => 'Albüm modelini seçin.'],
            ['title_like' => 'Paket',          'desc' => 'Albümünüzün satılacağı paket türünü seçin.'],
            ['title_like' => 'Pvc Kalın',      'desc' => 'Albüm kapağı için PVC kalınlığını seçin (firma yetkinize bağlı seçenekler gösterilir).'],
            ['title_like' => 'Albüm Üzerine',  'desc' => 'Albüm kapağına işlenmesini istediğiniz metni yazın (opsiyonel).'],
            ['title_like' => 'Yazılacak Yazı', 'desc' => 'Albüm kapağına işlenmesini istediğiniz metni yazın (opsiyonel).'],
            ['title_like' => 'Extra Ürün',     'desc' => 'Siparişinize eklemek istediğiniz ek ürünleri seçebilirsiniz (opsiyonel).'],
            ['title_like' => 'Çalışma Tipi',   'desc' => 'Tasarım üzerinde uygulanacak çalışma tipini seçin (Dizgi / Rötüş).'],
            ['title_like' => 'Not',            'desc' => 'Bu ürüne özel notunuzu yazabilirsiniz (opsiyonel).'],
        ];

        foreach ($rules as $rule) {
            DB::statement(
                "UPDATE customization_categories
                 SET description = ?
                 WHERE description IS NULL AND title LIKE ?",
                [$rule['desc'], '%' . $rule['title_like'] . '%']
            );
        }
    }

    public function down(): void
    {
        // Geri alma yok — admin panelden manuel temizlenebilir.
    }
};
