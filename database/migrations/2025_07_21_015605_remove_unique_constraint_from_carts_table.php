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
        Schema::table('carts', function (Blueprint $table) {
            // Önce foreign key'i kaldır
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            
            // Sonra unique constraint'i kaldır
            $table->dropUnique(['user_id', 'product_id']);
            
            // Foreign key'leri tekrar ekle (unique olmadan)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Önce foreign key'i kaldır
            $table->dropForeign(['user_id']);
            $table->dropForeign(['product_id']);
            
            // Unique constraint'i tekrar ekle
            $table->unique(['user_id', 'product_id']);
            
            // Foreign key'leri tekrar ekle
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
