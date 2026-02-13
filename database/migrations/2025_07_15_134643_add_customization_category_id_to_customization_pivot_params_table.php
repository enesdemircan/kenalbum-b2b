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
            if (!Schema::hasColumn('customization_pivot_params', 'customization_category_id')) {
                $table->unsignedBigInteger('customization_category_id')->nullable()->after('params_id');
                $table->foreign('customization_category_id')->references('id')->on('customization_categories')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customization_pivot_params', function (Blueprint $table) {
            if (Schema::hasColumn('customization_pivot_params', 'customization_category_id')) {
                $table->dropForeign(['customization_category_id']);
                $table->dropColumn('customization_category_id');
            }
        });
    }
};
