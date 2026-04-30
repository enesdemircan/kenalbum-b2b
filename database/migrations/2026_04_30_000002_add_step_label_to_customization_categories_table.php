<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customization_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('customization_categories', 'step_label')) {
                $table->string('step_label', 100)->nullable()->after('type');
            }
        });

        // Seed: existing kategorileri title pattern matching ile step'lere ata
        // file/files type olanlar NULL kalır (wizard'da render edilmez)
        $rules = [
            ['pattern' => 'Ebat',   'label' => 'Ebat & Paket'],
            ['pattern' => 'Boyut',  'label' => 'Ebat & Paket'],
            ['pattern' => 'Paket',  'label' => 'Ebat & Paket'],
            ['pattern' => 'Model',  'label' => 'Model'],
            ['pattern' => 'Kapak Modeli', 'label' => 'Model'],
            ['pattern' => 'Kumaş',  'label' => 'Kumaş'],
            ['pattern' => 'Kumas',  'label' => 'Kumaş'],
            ['pattern' => 'Renk',   'label' => 'Kumaş'],
        ];

        foreach ($rules as $rule) {
            DB::statement(
                "UPDATE customization_categories
                    SET step_label = ?
                    WHERE step_label IS NULL
                      AND type NOT IN ('file', 'files')
                      AND title LIKE ?",
                [$rule['label'], '%' . $rule['pattern'] . '%']
            );
        }

        // Geri kalan, file/files olmayan kategoriler → "Sipariş Detayı"
        DB::statement(
            "UPDATE customization_categories
                SET step_label = 'Sipariş Detayı'
                WHERE step_label IS NULL
                  AND type NOT IN ('file', 'files')"
        );
    }

    public function down(): void
    {
        Schema::table('customization_categories', function (Blueprint $table) {
            if (Schema::hasColumn('customization_categories', 'step_label')) {
                $table->dropColumn('step_label');
            }
        });
    }
};
