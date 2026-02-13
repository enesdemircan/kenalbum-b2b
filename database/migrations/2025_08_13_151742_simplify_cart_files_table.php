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
        // Tablo yoksa bu migration'ı atla
        if (!Schema::hasTable('cart_files')) {
            return;
        }
        
        Schema::table('cart_files', function (Blueprint $table) {
            // Kolonların varlığını kontrol ederek kaldır
            $columnsToCheck = [
                'original_filename',
                'file_size', 
                'file_type',
                'status',
                'customization_pivot_params_id',
                'file_order',
                'error_message'
            ];
            
            $columnsToDrop = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('cart_files', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_files', function (Blueprint $table) {
            // Eski sütunları geri ekle
            $table->string('original_filename')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->unsignedBigInteger('customization_pivot_params_id')->nullable();
            $table->string('file_order')->nullable();
            $table->text('error_message')->nullable();
        });
    }
};
