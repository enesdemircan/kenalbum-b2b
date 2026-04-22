<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customization_pivot_params', function (Blueprint $table) {
            if (!Schema::hasColumn('customization_pivot_params', 'is_required')) {
                $table->boolean('is_required')->default(false)->after('customization_params_ust_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customization_pivot_params', function (Blueprint $table) {
            if (Schema::hasColumn('customization_pivot_params', 'is_required')) {
                $table->dropColumn('is_required');
            }
        });
    }
};
