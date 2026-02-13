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
        Schema::create('customization_params_customers_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customization_params_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            // Foreign key relationships with custom names
            $table->foreign('customer_id', 'fk_cust_pivot_customer')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customization_params_id', 'fk_cust_pivot_param')->references('id')->on('customization_params')->onDelete('cascade');
            $table->foreign('product_id', 'fk_cust_pivot_product')->references('id')->on('products')->onDelete('cascade');

            // Add unique constraint to prevent duplicate entries
            $table->unique(['customer_id', 'customization_params_id', 'product_id'], 'customization_params_customers_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customization_params_customers_pivot');
    }
};
