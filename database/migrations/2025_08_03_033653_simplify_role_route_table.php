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
        Schema::table('role_route', function (Blueprint $table) {
            // can_ ile başlayan sütunları kaldır
            $table->dropColumn([
                'can_access',
                'can_create', 
                'can_read',
                'can_update',
                'can_delete'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_route', function (Blueprint $table) {
            // Geri almak için sütunları tekrar ekle
            $table->boolean('can_access')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
        });
    }
};
