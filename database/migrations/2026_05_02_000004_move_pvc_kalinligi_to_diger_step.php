<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Pvc Kalınlığı kategorisi (type=hidden, firma bazlı erişim) ayrı bir tab
 * yerine "Diğer" tab'ında gruplansın. Hidden type access kontrolü
 * OrderController::shouldShowHidden() üzerinden zaten çalışıyor — yetkisi
 * olan müşteriler Diğer tab'ı içinde Pvc seçimi yapar, olmayan müşteri
 * için Pvc section'ı render edilmez ama Diğer tab'ı sabit alanlarla görünür.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('customization_categories')
            ->where('title', 'LIKE', '%Pvc Kalın%')
            ->update(['step_label' => 'Diğer']);
    }

    public function down(): void
    {
        DB::table('customization_categories')
            ->where('title', 'LIKE', '%Pvc Kalın%')
            ->update(['step_label' => 'Pvc Kalınlığı']);
    }
};
