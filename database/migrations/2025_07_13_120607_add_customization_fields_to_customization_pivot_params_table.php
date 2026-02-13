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
        Schema::table('customization_pivot_params', function (Blueprint $table) {
            $table->unsignedBigInteger('customization_category_id')->nullable()->after('params_id');
            $table->unsignedBigInteger('customization_params_ust_id')->default(0)->after('customization_category_id');
            
            $table->foreign('customization_category_id')->references('id')->on('customization_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customization_pivot_params', function (Blueprint $table) {
            $table->dropForeign(['customization_category_id']);
            $table->dropColumn(['customization_category_id', 'customization_params_ust_id']);
        });
    }
};
