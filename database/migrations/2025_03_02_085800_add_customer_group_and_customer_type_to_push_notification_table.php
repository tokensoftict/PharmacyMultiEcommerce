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
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->foreignId('customer_type_id')->after('app_id')->nullable()->constrained();
            $table->foreignId('customer_group_id')->after('customer_type_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_type_id');
            $table->dropConstrainedForeignId('customer_group_id');
        });
    }
};
