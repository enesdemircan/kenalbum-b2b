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
        Schema::create('role_route', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->boolean('can_access')->default(true); // Erişim izni
            $table->boolean('can_create')->default(false); // Oluşturma izni
            $table->boolean('can_read')->default(false); // Okuma izni
            $table->boolean('can_update')->default(false); // Güncelleme izni
            $table->boolean('can_delete')->default(false); // Silme izni
            $table->timestamps();
            
            // Aynı role ve route kombinasyonu tekrar olmasın
            $table->unique(['role_id', 'route_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_route');
    }
};
