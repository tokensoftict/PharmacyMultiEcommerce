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
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('minimum_amount', 15, 2)->default(0)->after('type_value');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('minimum_amount', 15, 2)->default(0)->after('type_value');
        });

        Schema::table('voucher_codes', function (Blueprint $table) {
            $table->decimal('minimum_amount', 15, 2)->default(0)->after('type_value');
        });

        Schema::table('order_total_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id')->nullable()->after('value');
            $table->string('discount_type')->nullable()->after('discount_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('minimum_amount');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('minimum_amount');
        });

        Schema::table('voucher_codes', function (Blueprint $table) {
            $table->dropColumn('minimum_amount');
        });

        Schema::table('order_total_orders', function (Blueprint $table) {
            $table->dropColumn(['discount_id', 'discount_type']);
        });
    }
};
