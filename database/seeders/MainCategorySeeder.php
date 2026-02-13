<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MainCategory;

class MainCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ana kategoriler
        $albümler = MainCategory::create([
            'ust_id' => 0,
            'title' => 'Albümler',
            'order' => 0
        ]);

        $fotokitap = MainCategory::create([
            'ust_id' => 0,
            'title' => 'Fotokitap',
            'order' => 0
        ]);

        $okulUrunleri = MainCategory::create([
            'ust_id' => 0,
            'title' => 'Okul Ürünleri',
            'order' => 0
        ]);

        $ekstraBaskiUrunler = MainCategory::create([
            'ust_id' => 0,
            'title' => 'Ekstra Baskı Ürünler',
            'order' => 0
        ]);

        // Alt kategoriler
        MainCategory::create([
            'ust_id' => $fotokitap->id,
            'title' => 'Mat Lustre Fotoğraf Kağıtlı Fotokitap',
            'order' => 0
        ]);

        MainCategory::create([
            'ust_id' => $fotokitap->id,
            'title' => 'Ofset Dijital Baskılı Fotokitap',
            'order' => 0
        ]);

        MainCategory::create([
            'ust_id' => $okulUrunleri->id,
            'title' => 'Kep Detaylı Ahşap 3lü Deri',
            'order' => 0
        ]);

        MainCategory::create([
            'ust_id' => $ekstraBaskiUrunler->id,
            'title' => 'Kanvaslar',
            'order' => 0
        ]);

        MainCategory::create([
            'ust_id' => $ekstraBaskiUrunler->id,
            'title' => 'Büyük Ebat Baskı',
            'order' => 0
        ]);

        MainCategory::create([
            'ust_id' => $ekstraBaskiUrunler->id,
            'title' => 'Albüm Ekstra Ürünler',
            'order' => 0
        ]);
    }
}
