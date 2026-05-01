<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Bayi adres tipleri:
 * - 'company'  → Şirket Adresim (Bana Gelsin) — bayi kendi depo/şirket adresine
 * - 'customer' → Müşteri Adresi (Müşterime Gitsin) — nihai müşteriye direkt
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->enum('type', ['company', 'customer'])->default('company')->after('user_id');
        });

        // Mevcut tüm adresler 'company' (bayi şirket adresi) olarak başlar
        DB::table('user_addresses')->update(['type' => 'company']);
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
