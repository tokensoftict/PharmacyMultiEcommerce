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
        Schema::create('coupon_usage_histories', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->date('use_date');
            $table->foreignId("coupon_id")->constrained()->cascadeOnDelete();
            $table->foreignId("app_id")->constrained()->cascadeOnDelete();
            $table->morphs('user_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usage_histories');
    }
};
