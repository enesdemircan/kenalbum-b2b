<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZipFieldsToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'local_zip')) {
                $table->string('local_zip')->nullable()->after('images');
            }
            if (!Schema::hasColumn('carts', 's3_zip')) {
                $table->string('s3_zip')->nullable()->after('local_zip');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['local_zip', 's3_zip']);
        });
    }
} 