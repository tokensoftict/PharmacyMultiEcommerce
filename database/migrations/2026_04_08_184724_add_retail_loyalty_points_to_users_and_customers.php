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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('retail_loyalty_points', 15, 2)->default(0)->after('loyalty_points');
        });

        Schema::table('local_customers', function (Blueprint $table) {
            $table->decimal('retail_loyalty_points', 15, 2)->default(0)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('retail_loyalty_points');
        });

        Schema::table('local_customers', function (Blueprint $table) {
            $table->dropColumn('retail_loyalty_points');
        });
    }
};
