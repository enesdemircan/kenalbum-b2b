<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('customization_categories')
            ->where('step_label', 'Özel')
            ->update(['step_label' => 'Diğer']);
    }

    public function down(): void
    {
        DB::table('customization_categories')
            ->where('step_label', 'Diğer')
            ->update(['step_label' => 'Özel']);
    }
};
