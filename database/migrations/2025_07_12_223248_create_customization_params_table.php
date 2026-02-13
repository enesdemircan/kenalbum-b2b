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
        Schema::create('customization_params', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ust_id')->default(0); // ana parametre ise 0
            $table->unsignedBigInteger('customization_category_id');
            $table->string('key');
            $table->string('value');
            $table->integer('order')->default(0);
            $table->string('option1')->nullable();
            $table->string('option2')->nullable();
            $table->timestamps();

            $table->foreign('customization_category_id')->references('id')->on('customization_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customization_params');
    }
};
