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
        Schema::table('wholesales_users', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->after('address_id')->nullable()->constrained('payment_methods');
            $table->foreignId('delivery_method_id')->after('address_id')->nullable()->constrained('delivery_methods');
        });

        Schema::table('supermarket_users', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->after('address_id')->nullable()->constrained('payment_methods');
            $table->foreignId('delivery_method_id')->after('address_id')->nullable()->constrained('delivery_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wholesales_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropConstrainedForeignId('delivery_method_id');
        });

        Schema::table('supermarket_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropConstrainedForeignId('delivery_method_id');
        });
    }
};
