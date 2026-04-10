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
        Schema::table('member_groups', function (Blueprint $table) {
            $table->string('card_gradient_start')->nullable()->after('bg_color');
            $table->string('card_gradient_end')->nullable()->after('card_gradient_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_groups', function (Blueprint $table) {
            $table->dropColumn(['card_gradient_start', 'card_gradient_end']);
        });
    }
};
