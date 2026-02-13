<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mevcut ürünlere slug ekle
        $products = \App\Models\Product::whereNull('slug')->orWhere('slug', '')->get();
        
        foreach ($products as $product) {
            $slug = \Illuminate\Support\Str::slug($product->title, '-', 'tr');
            
            // Eğer slug boşsa, title'ı kullan
            if (empty($slug)) {
                $slug = 'urun-' . $product->id;
            }
            
            // Eğer slug zaten varsa, sonuna sayı ekle
            $originalSlug = $slug;
            $counter = 1;
            
            while (\App\Models\Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $product->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Bu migration'ı geri almak için bir şey yapmaya gerek yok
    }
};
