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
        Schema::table('carts', function (Blueprint $table) {
            // Remove size and color columns
            $table->dropColumn(['size', 'color']);
            
            // Add quantity column (already exists, but let's make sure it's properly configured)
            if (!Schema::hasColumn('carts', 'quantity')) {
                $table->integer('quantity')->default(1)->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Add back size and color columns
            $table->string('size')->nullable()->after('price');
            $table->string('color')->nullable()->after('size');
            
            // Remove quantity column if it was added
            if (Schema::hasColumn('carts', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
