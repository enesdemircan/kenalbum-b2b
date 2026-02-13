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
        Schema::table('discount_groups', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('main_category_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
