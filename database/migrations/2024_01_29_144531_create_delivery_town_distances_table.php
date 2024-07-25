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
        Schema::create('delivery_town_distances', function (Blueprint $table) {
            $table->id();
            $table->decimal('town_distance')->default(0);
            $table->foreignId('town_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('ALLOW_WHEN_CLOSE_TO_DATE');
            $table->string("frequency")->default("days");
            $table->integer("no")->default("1");
            $table->decimal('minimum_shipping_amount')->default(0);
            $table->decimal('fixed_shipping_amount')->default(0);
            $table->string('delivery_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_town_distances');
    }
};
