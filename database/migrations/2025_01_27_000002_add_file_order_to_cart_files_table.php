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
            $table->integer('file_order')->default(1)->after('customization_pivot_params_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_files', function (Blueprint $table) {
            $table->dropColumn('file_order');
        });
    }
}; 