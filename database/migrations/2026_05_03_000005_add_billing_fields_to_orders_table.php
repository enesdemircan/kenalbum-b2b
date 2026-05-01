<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sipariş fatura adresi alanları — checkout'ta kullanıcı 'fatura adresim
 * teslimat adresimle aynı' işaretlemezse ayrı doldurur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('billing_name')->nullable()->after('district');
            $table->string('billing_surname')->nullable()->after('billing_name');
            $table->string('billing_phone')->nullable()->after('billing_surname');
            $table->string('billing_city')->nullable()->after('billing_phone');
            $table->string('billing_district')->nullable()->after('billing_city');
            $table->text('billing_address')->nullable()->after('billing_district');
            $table->string('billing_tax_no', 50)->nullable()->after('billing_address');
            $table->string('billing_company')->nullable()->after('billing_tax_no');
            $table->boolean('billing_same_as_shipping')->default(true)->after('billing_company');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'billing_name', 'billing_surname', 'billing_phone',
                'billing_city', 'billing_district', 'billing_address',
                'billing_tax_no', 'billing_company', 'billing_same_as_shipping',
            ]);
        });
    }
};
