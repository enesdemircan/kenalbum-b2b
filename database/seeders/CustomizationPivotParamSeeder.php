<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomizationPivotParam;
use App\Models\Product;
use App\Models\CustomizationParam;

class CustomizationPivotParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        if ($products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            // Ana parametreler için pivot kayıtları oluştur
            $mainParams = CustomizationParam::whereIn('customization_category_id', [1, 2, 3, 5, 6, 7, 8, 10])->get();
            
            foreach ($mainParams as $param) {
                CustomizationPivotParam::create([
                    'product_id' => $product->id,
                    'params_id' => $param->id,
                    'price' => 0, // Varsayılan fiyat
                    'customization_category_id' => $param->customization_category_id,
                    'customization_params_ust_id' => $param->ust_id
                ]);
            }

            // Kumaş parametrelerinin child'ı olan renkler için pivot kayıtları oluştur
            $kumasCategory = \App\Models\CustomizationCategory::where('title', 'Kumaş Seçimi')->first();
            $kumasParams = CustomizationParam::where('customization_category_id', $kumasCategory->id)->get();
            
            foreach ($kumasParams as $kumasParam) {
                $renkChildren = CustomizationParam::where('ust_id', $kumasParam->id)->get();
                
                foreach ($renkChildren as $renkParam) {
                    CustomizationPivotParam::create([
                        'product_id' => $product->id,
                        'params_id' => $renkParam->id,
                        'price' => 0, // Renk seçimi ücretsiz
                        'customization_category_id' => $renkParam->customization_category_id,
                        'customization_params_ust_id' => $renkParam->ust_id
                    ]);
                }
            }
        }
    }
} 