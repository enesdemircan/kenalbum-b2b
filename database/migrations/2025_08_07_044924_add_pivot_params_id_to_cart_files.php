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
            $table->unsignedBigInteger('customization_pivot_params_id')->nullable()->after('cart_id');
            $table->foreign('customization_pivot_params_id')->references('id')->on('customization_pivot_params')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_files', function (Blueprint $table) {
            $table->dropForeign(['customization_pivot_params_id']);
            $table->dropColumn('customization_pivot_params_id');
        });
    }
};
