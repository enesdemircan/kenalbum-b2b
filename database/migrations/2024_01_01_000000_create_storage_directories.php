<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CreateStorageDirectories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Chunk upload sistemi için gerekli dizinleri oluştur
        $directories = [
            'chunks',
            'merged',
            'zips'
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dizinleri temizle
        $directories = [
            'chunks',
            'merged',
            'zips'
        ];

        foreach ($directories as $directory) {
            if (Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->deleteDirectory($directory);
            }
        }
    }
} 