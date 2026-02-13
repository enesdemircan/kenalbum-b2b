<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\MainCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Albümler kategorisini bul
        $albümlerCategory = MainCategory::where('title', 'Albümler')->first();

        // Düğün Albümü ürününü ekle
        Product::create([
            'title' => 'Düğün Albümü',
            'price' => 1200.00,
            'main_category_id' => $albümlerCategory->id,
            'ust_id' => null,
            'images' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=500&h=500&fit=crop',
            'status' => 1,
            'order' => 0,
            'price_difference_per_page' => 7,
            'min_pages' => 5,
            'max_pages' => 20,

            'suggested_products' => null
        ]);

    }
}
