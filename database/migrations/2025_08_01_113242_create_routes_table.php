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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Route name (örn: admin.products.index)
            $table->string('uri'); // Route URI (örn: /admin/products)
            $table->string('method'); // HTTP method (GET, POST, PUT, DELETE)
            $table->string('group')->nullable(); // Route group (admin, customer, etc.)
            $table->string('description')->nullable(); // Route açıklaması
            $table->boolean('is_active')->default(true); // Route aktif mi?
            $table->timestamps();
            
            // Name ve method birlikte unique olmalı
            $table->unique(['name', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
