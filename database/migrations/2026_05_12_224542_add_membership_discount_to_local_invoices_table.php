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
        Schema::table('local_invoices', function (Blueprint $blueprint) {
            $blueprint->decimal('membership_discount', 20, 2)->default(0)->after('total');
            $blueprint->decimal('membership_discount_value', 20, 2)->default(0)->after('membership_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('local_invoices', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['membership_discount', 'membership_discount_value']);
        });
    }
};
