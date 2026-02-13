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
        Schema::create('discount_group_customer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_group_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();
            
            $table->foreign('discount_group_id')->references('id')->on('discount_groups')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            $table->unique(['discount_group_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_group_customer');
    }
};
