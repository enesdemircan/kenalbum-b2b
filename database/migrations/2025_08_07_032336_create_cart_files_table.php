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
        Schema::create('cart_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->string('local_file_url')->nullable();
            $table->string('s3_url')->nullable();
            $table->string('original_filename');
            $table->string('file_size');
            $table->string('file_type');
            $table->enum('status', ['pending', 'uploading', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->index(['cart_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_files');
    }
};
