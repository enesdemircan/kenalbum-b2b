<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Çalışma Tipi (Dizgi / Rötüş) pivotlarına default ek ücret yaz:
 * - Dizgi: 300 TL
 * - Rötüş: 500 TL
 *
 * Sadece price NULL olan pivotlara yazılır — admin panelden değiştirilmiş
 * fiyatları override etmez. Title üzerinden eşleştirilir (kategori ID
 * ortama göre değişebilir).
 */
return new class extends Migration
{
    public function up(): void
    {
        $rules = [
            ['title' => 'Dizgi', 'price' => 300.00],
            ['title' => 'Rötüş', 'price' => 500.00],
        ];

        foreach ($rules as $rule) {
            DB::statement(
                "UPDATE customization_pivot_params cpp
                 INNER JOIN customization_params cp ON cp.id = cpp.params_id
                 INNER JOIN customization_categories cc ON cc.id = cpp.customization_category_id
                 SET cpp.price = ?
                 WHERE cpp.price IS NULL
                   AND cp.`key` = ?
                   AND cc.title = 'Çalışma Tipi'",
                [$rule['price'], $rule['title']]
            );
        }
    }

    public function down(): void
    {
        // Geri alma yok — admin panelden temizlenebilir.
    }
};
