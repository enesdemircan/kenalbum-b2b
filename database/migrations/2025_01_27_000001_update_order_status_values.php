<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mevcut status değerlerini güncelle
        // 0: İşlemde -> 1: İşlemde
        // 1: Teslim Edildi -> 2: Teslim Edildi  
        // 2: İptal -> 3: İptal
        // Yeni: 0: Onay Bekliyor
        
        DB::statement("UPDATE orders SET status = CASE 
            WHEN status = 0 THEN 1  -- İşlemde -> İşlemde
            WHEN status = 1 THEN 2  -- Teslim Edildi -> Teslim Edildi
            WHEN status = 2 THEN 3  -- İptal -> İptal
            ELSE status
        END");
        
        // Yeni siparişler için default değeri 0 (Onay Bekliyor) yap
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->change();
            // 0: Onay Bekliyor, 1: İşlemde, 2: Teslim Edildi, 3: İptal
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eski değerlere geri dön
        DB::statement("UPDATE orders SET status = CASE 
            WHEN status = 1 THEN 0  -- İşlemde -> İşlemde (eski)
            WHEN status = 2 THEN 1  -- Teslim Edildi -> Teslim Edildi (eski)
            WHEN status = 3 THEN 2  -- İptal -> İptal (eski)
            ELSE status
        END");
        
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->change();
            // 0: İşlemde, 1: Teslim Edildi, 2: İptal (eski hali)
        });
    }
}; 