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
        Schema::table('delivery_town_distances', function (Blueprint $table) {
            $table->string('delivery_type', 5)->default('0')->after('delivery_days');
            $table->integer('interval_no')->nullable()->after('type');
            $table->integer('reset_time_days')->nullable()->after('interval_no');
            $table->string('interval_frequency', 30)->nullable()->after('interval_no');
            $table->date('starting_date')->nullable()->after('interval_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_town_distances', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'interval_no', 'interval_frequency', 'starting_date']);
        });
    }
};
