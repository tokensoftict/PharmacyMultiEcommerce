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
            $table->date('order_valid_from')->nullable()->after('valid_to');
            $table->date('order_valid_to')->nullable()->after('order_valid_from');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->date('order_valid_from')->nullable()->after('valid_to');
            $table->date('order_valid_to')->nullable()->after('order_valid_from');
        });

        Schema::table('voucher_codes', function (Blueprint $table) {
            $table->date('order_valid_from')->nullable()->after('valid_to');
            $table->date('order_valid_to')->nullable()->after('order_valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['order_valid_from', 'order_valid_to']);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['order_valid_from', 'order_valid_to']);
        });

        Schema::table('voucher_codes', function (Blueprint $table) {
            $table->dropColumn(['order_valid_from', 'order_valid_to']);
        });
    }
};
