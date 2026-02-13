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
        Schema::create('file_upload_queue', function (Blueprint $table) {
            $table->id();
            $table->string('queue_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('param_id');
            $table->unsignedBigInteger('category_id');
            $table->json('file_data');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('s3_path')->nullable();
            $table->string('s3_url')->nullable();
            $table->integer('file_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('param_id')->references('id')->on('customization_params')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('customization_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_upload_queue');
    }
};
