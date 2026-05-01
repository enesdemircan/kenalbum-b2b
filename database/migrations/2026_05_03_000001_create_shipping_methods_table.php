<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('title');                                // "Aras Kargo"
            $table->string('code', 50)->unique();                   // 'aras', 'yurtici'
            $table->decimal('price', 10, 2)->default(0);            // ek ücret
            $table->text('description')->nullable();                // "1-2 iş günü teslimat"
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
