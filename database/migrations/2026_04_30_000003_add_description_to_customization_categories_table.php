<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customization_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('customization_categories', 'description')) {
                $table->text('description')->nullable()->after('step_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customization_categories', function (Blueprint $table) {
            if (Schema::hasColumn('customization_categories', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
