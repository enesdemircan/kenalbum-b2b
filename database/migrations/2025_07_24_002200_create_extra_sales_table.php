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
        Schema::create('extra_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('child_product_id')->constrained('products')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Aynı ana ürün ve child ürün kombinasyonunun tekrar olmaması için
            $table->unique(['main_product_id', 'child_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_sales');
    }
};
