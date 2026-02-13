<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomizationCategory;

class CustomizationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ebat kategorisi
        $ebat = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Ebat',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Baskı Laminasyon kategorisi
        $baski = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Baskı Laminasyon',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Kumaş Seçimi kategorisi
        $kumas = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Kumaş Seçimi',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Renk Seçimi kategorisi (Kumaş'ın child'ı)
        $renk = CustomizationCategory::create([
            'ust_id' => $kumas->id,
            'title' => 'Renk Seçimi',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Paket İçeriği kategorisi
        $paket = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Paket İçeriği',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Kutu Seçeneği kategorisi
        $kutu = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Kutu Seçeneği',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);


        // Kanvas kategorisi
        $kanvas = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Kanvas',
            'type' => 'radio',
            'required' => 1,
            'order' => 0
        ]);

        // Özel Metin Alanı kategorisi
        $ozelMetin = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Özel Metin Alanı',
            'type' => 'input',
            'required' => 1,
            'order' => 0
        ]);

        // Extra Ürün kategorisi
        $extraUrun = CustomizationCategory::create([
            'ust_id' => 0,
            'title' => 'Extra Ürün',
            'type' => 'checkbox',
            'required' => 1,
            'order' => 0
        ]);
    }
}
