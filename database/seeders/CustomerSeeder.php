<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'firma_id' => 'FIRMA001',
            'unvan' => 'ABC Teknoloji A.Ş.',
            'phone' => '0212 555 0001',
            'email' => 'info@abcteknoloji.com',
            'adres' => 'Levent Mahallesi, Teknoloji Caddesi No:123, Beşiktaş/İstanbul',
            'vergi_dairesi' => 'Beşiktaş',
            'vergi_numarasi' => '1234567890',
        ]);

        Customer::create([
            'firma_id' => 'FIRMA002',
            'unvan' => 'XYZ Medya Ltd. Şti.',
            'phone' => '0216 555 0002',
            'email' => 'iletisim@xyzmedya.com',
            'adres' => 'Kadıköy Mahallesi, Medya Sokak No:45, Kadıköy/İstanbul',
            'vergi_dairesi' => 'Kadıköy',
            'vergi_numarasi' => '9876543210',
        ]);

        Customer::create([
            'firma_id' => 'FIRMA003',
            'unvan' => 'DEF İnşaat ve Yapı A.Ş.',
            'phone' => '0312 555 0003',
            'email' => 'info@definsaat.com',
            'adres' => 'Çankaya Mahallesi, İnşaat Bulvarı No:67, Çankaya/Ankara',
            'vergi_dairesi' => 'Çankaya',
            'vergi_numarasi' => '4567891230',
        ]);
    }
}
