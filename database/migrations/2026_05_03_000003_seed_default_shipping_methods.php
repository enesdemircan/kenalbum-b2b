<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $rows = [
            ['title' => 'Aras Kargo',     'code' => 'aras',     'price' => 60.00,  'description' => '1-2 iş günü teslimat',      'is_active' => 1, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Yurtiçi Kargo',  'code' => 'yurtici',  'price' => 60.00,  'description' => '1-2 iş günü teslimat',      'is_active' => 1, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'MNG Kargo',      'code' => 'mng',      'price' => 60.00,  'description' => '1-3 iş günü teslimat',      'is_active' => 1, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'PTT Kargo',      'code' => 'ptt',      'price' => 50.00,  'description' => '2-4 iş günü teslimat',      'is_active' => 1, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Bayi Getirecek', 'code' => 'pickup',   'price' => 0.00,   'description' => 'Atölyemizden teslim alma',  'is_active' => 1, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($rows as $row) {
            DB::table('shipping_methods')->insertOrIgnore($row);
        }
    }

    public function down(): void
    {
        DB::table('shipping_methods')->whereIn('code', ['aras','yurtici','mng','ptt','pickup'])->delete();
    }
};
