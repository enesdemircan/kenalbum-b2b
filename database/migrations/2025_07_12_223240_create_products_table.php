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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('main_category_id');
            $table->text('images')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('order')->default(0);
            $table->integer('price_difference_per_page')->default(0);
            $table->integer('max_pages')->nullable();
            $table->integer('min_pages')->nullable();
            $table->string('option1')->nullable();
            $table->string('option2')->nullable(); 
            $table->timestamps();
            
            $table->foreign('main_category_id')->references('id')->on('main_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
