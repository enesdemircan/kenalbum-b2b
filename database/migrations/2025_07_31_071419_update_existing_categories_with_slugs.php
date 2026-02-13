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
        // Mevcut kategorilere slug ekle
        $categories = \App\Models\MainCategory::whereNull('slug')->orWhere('slug', '')->get();
        
        foreach ($categories as $category) {
            $slug = \Illuminate\Support\Str::slug($category->title, '-', 'tr');
            
            // Eğer slug boşsa, title'ı kullan
            if (empty($slug)) {
                $slug = 'kategori-' . $category->id;
            }
            
            // Eğer slug zaten varsa, sonuna sayı ekle
            $originalSlug = $slug;
            $counter = 1;
            
            while (\App\Models\MainCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $category->update(['slug' => $slug]);
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
