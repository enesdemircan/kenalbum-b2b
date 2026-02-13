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
            $table->unsignedBigInteger('discount_group_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0); // İndirim tutarı
            $table->decimal('original_total', 10, 2)->default(0); // İndirim öncesi toplam
            $table->decimal('final_total', 10, 2)->default(0); // İndirim sonrası toplam
            
            $table->foreign('discount_group_id')->references('id')->on('discount_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['discount_group_id']);
            $table->dropColumn(['discount_group_id', 'discount_amount', 'original_total', 'final_total']);
        });
    }
};
