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
        \DB::table('order_totals')->insert([
            'title' => 'Membership Discount',
            'order_total_type' => 'Percentage',
            'code' => 'membership_discount',
            'status' => 1,
            'value' => 0, // Value will be dynamic
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('order_totals')->where('code', 'membership_discount')->delete();
    }
};
