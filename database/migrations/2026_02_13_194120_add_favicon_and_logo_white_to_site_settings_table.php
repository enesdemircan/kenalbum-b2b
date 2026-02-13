<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // logo_white ve favicon kolonlarının olup olmadığını kontrol et
            if (!Schema::hasColumn('site_settings', 'logo_white')) {
                $table->string('logo_white')->nullable()->after('logo');
            }
            
            if (!Schema::hasColumn('site_settings', 'favicon')) {
                $table->string('favicon')->nullable()->after('logo_white');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'logo_white')) {
                $table->dropColumn('logo_white');
            }
            
            if (Schema::hasColumn('site_settings', 'favicon')) {
                $table->dropColumn('favicon');
            }
        });
    }
};
