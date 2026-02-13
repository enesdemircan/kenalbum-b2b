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
        Schema::table('cart_files', function (Blueprint $table) {
            // Status sütununu güncelle - 'uploaded' değerini kabul etsin
            $table->enum('status', ['uploading', 'uploaded', 'processing', 'completed', 'failed'])->default('uploading')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_files', function (Blueprint $table) {
            // Status sütununu eski haline getir
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->change();
        });
    }
};
