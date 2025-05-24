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
        Schema::table('supermarket_users', function (Blueprint $table) {
            $table->foreignId('local_customer_id')->nullable()->after('phone');
        });

        Schema::table('wholesales_users', function (Blueprint $table) {
            $table->foreignId('local_customer_id')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supermarket_users', function (Blueprint $table) {
            $table->dropForeign('local_customer_id');
        });

        Schema::table('wholesales_users', function (Blueprint $table) {
            $table->dropForeign('local_customer_id');
        });
    }
};
