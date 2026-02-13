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
        Schema::table('order_status_histories', function (Blueprint $table) {
            // cart_id alanını ekle
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_histories', function (Blueprint $table) {
            // cart_id alanını kaldır
            $table->dropForeign(['cart_id']);
            $table->dropColumn('cart_id');
        });
    }
};
