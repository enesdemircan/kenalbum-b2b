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
        Schema::create('discount_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // İndirim grubu adı
            $table->text('description')->nullable(); // Açıklama
            $table->decimal('discount_percentage', 5, 2); // İndirim yüzdesi (örn: 10.00)
            $table->unsignedBigInteger('main_category_id'); // Hangi ana kategoriye uygulanacak
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->date('start_date')->nullable(); // Başlangıç tarihi
            $table->date('end_date')->nullable(); // Bitiş tarihi
            $table->timestamps();
            
            $table->foreign('main_category_id')->references('id')->on('main_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_groups');
    }
};
