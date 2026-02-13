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
        Schema::create('customization_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ust_id')->default(0); // ana kategori ise 0
            $table->string('title');
            $table->string('type'); // select, input vb.
            $table->boolean('required')->default(0);
            $table->integer('order')->default(0);
            $table->string('option1')->nullable();
            $table->string('option2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customization_categories');
    }
};
