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
        Schema::table('orders', function (Blueprint $table) {
            // Eski sütunları kaldır
            $table->dropForeign(['product_id']);
            $table->dropForeign(['discount_group_id']);
            $table->dropColumn(['product_id', 'customizations', 'page_count', 'discount_group_id', 'discount_amount', 'original_total', 'final_total']);
            
            // Yeni sütunları ekle
            $table->string('order_number')->unique()->after('user_id');
            $table->string('customer_name')->after('order_number');
            $table->string('customer_surname')->after('customer_name');
            $table->string('customer_phone')->after('customer_surname');
            $table->text('shipping_address')->after('customer_phone');
            $table->enum('payment_method', ['havale'])->default('havale')->after('shipping_address');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending')->after('payment_method');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Yeni sütunları kaldır
            $table->dropColumn(['order_number', 'customer_name', 'customer_surname', 'customer_phone', 'shipping_address', 'payment_method', 'status', 'notes']);
            
            // Eski sütunları geri ekle
            $table->unsignedBigInteger('product_id');
            $table->json('customizations');
            $table->integer('page_count')->nullable();
            $table->unsignedBigInteger('discount_group_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('original_total', 10, 2)->default(0.00);
            $table->decimal('final_total', 10, 2)->default(0.00);
            
            // Foreign key'leri geri ekle
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('discount_group_id')->references('id')->on('discount_groups');
        });
    }
};
