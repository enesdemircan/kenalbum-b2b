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
        Schema::table('cart_files', function (Blueprint $table) {
            // Eksik alanları ekle
            if (!Schema::hasColumn('cart_files', 'file_name')) {
                $table->string('file_name')->after('cart_id');
            }
            if (!Schema::hasColumn('cart_files', 'category_id')) {
                $table->string('category_id')->after('file_name');
            }
            if (!Schema::hasColumn('cart_files', 'file_type')) {
                $table->enum('file_type', ['file', 'files'])->default('file')->after('category_id');
            }
            if (!Schema::hasColumn('cart_files', 'chunk_index')) {
                $table->integer('chunk_index')->default(0)->after('file_type');
            }
            if (!Schema::hasColumn('cart_files', 'total_chunks')) {
                $table->integer('total_chunks')->default(1)->after('chunk_index');
            }
            if (!Schema::hasColumn('cart_files', 'chunk_path')) {
                $table->string('chunk_path')->nullable()->after('total_chunks');
            }
            if (!Schema::hasColumn('cart_files', 'file_order')) {
                $table->json('file_order')->nullable()->after('chunk_path');
            }
            if (!Schema::hasColumn('cart_files', 'status')) {
                $table->enum('status', ['uploading', 'uploaded', 'processing', 'completed', 'failed'])->default('uploading')->after('file_order');
            }
            if (!Schema::hasColumn('cart_files', 's3_path')) {
                $table->string('s3_path')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_files', function (Blueprint $table) {
            // Eklenen alanları kaldır
            $table->dropColumn([
                'file_name',
                'category_id',
                'file_type',
                'chunk_index',
                'total_chunks',
                'chunk_path',
                'file_order',
                'status',
                's3_path'
            ]);
        });
    }
};
