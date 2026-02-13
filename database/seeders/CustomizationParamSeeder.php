<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomizationParam;
use App\Models\CustomizationCategory;

class CustomizationParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ebat kategorisini bul
        $ebatCategory = CustomizationCategory::where('title', 'Ebat')->first();
        
        // Ebat parametreleri
        $ebatParams = [
            '25x65 Albüm',
            '30x50 Albüm',
            '30x60 Albüm',
            '30x76 Albüm',
            '30x80 Albüm',
            '35x65 Albüm'
        ];

        foreach ($ebatParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $ebatCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }

        // Baskı Laminasyon kategorisini bul
        $baskiCategory = CustomizationCategory::where('title', 'Baskı Laminasyon')->first();
        
        // Baskı Laminasyon parametreleri
        $baskiParams = [
            'Mat (Lustre)',
            'İpek'
        ];

        foreach ($baskiParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $baskiCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }

        // Kumaş Seçimi kategorisini bul
        $kumasCategory = CustomizationCategory::where('title', 'Kumaş Seçimi')->first();
        
        // Kumaş Seçimi parametreleri
        $kumasParams = [
            'Nubuk',
            'Keten',
            'Kadife/Kumaş',
            'Süet'
        ];

        $createdKumasParams = [];
        foreach ($kumasParams as $param) {
            $createdParam = CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $kumasCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
            $createdKumasParams[] = $createdParam;
        }

      

        // Paket İçeriği kategorisini bul
        $paketCategory = CustomizationCategory::where('title', 'Paket İçeriği')->first();
        
        // Paket İçeriği parametreleri
        $paketParams = [
            'Tek Albüm',
            'Set (2 Jumbo Albüm 1 50x60 Poster)'
        ];

        foreach ($paketParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $paketCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }

        // Kutu Seçeneği kategorisini bul
        $kutuCategory = CustomizationCategory::where('title', 'Kutu Seçeneği')->first();
        
        // Kutu Seçeneği parametreleri
        $kutuParams = [
            'Demonte Kutu İstemiyorum',
            'Demonte Kutu'
        ];

        foreach ($kutuParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $kutuCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }

        // Kanvas kategorisini bul
        $kanvasCategory = CustomizationCategory::where('title', 'Kanvas')->first();
        
        // Kanvas parametreleri
        $kanvasParams = [
            'Kanvas Yok',
            '50x60 Kanvas',
            '50x70 Kanvas'
        ];

        foreach ($kanvasParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $kanvasCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }

        // Extra Ürün kategorisini bul
        $extraUrunCategory = CustomizationCategory::where('title', 'Extra Ürün')->first();
        
        // Extra Ürün parametreleri
        $extraUrunParams = [
            'Cep Albümü'
        ];

        foreach ($extraUrunParams as $param) {
            CustomizationParam::create([
                'ust_id' => 0,
                'customization_category_id' => $extraUrunCategory->id,
                'key' => $param,
                'value' => '',
                'order' => 0
            ]);
        }
    }
}
