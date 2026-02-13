<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
          
            'Onay Bekliyor',
            'İşlem Sırasına Alındı',
            'Fotoğraflar Baskıya Alındı',
            'Kapak Tasarımı Yapılıyor',
            'Kalite Kontrol Biriminde',
            'Kargoya Hazır',
            'Kargoya Verildi',
            'Servise Verildi',
            'Sipariş İptal Edildi',
            'Tamamlandı'
        ];

        foreach ($statuses as $status) {
            DB::table('order_statuses')->insert([
                'title' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 