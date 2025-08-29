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
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->unsignedInteger('old_server_id')->nullable()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_representatives', function (Blueprint $table) {
            $table->dropColumn('old_server_id');
        });
    }
};
