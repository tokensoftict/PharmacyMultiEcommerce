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
        Schema::create('supermarkets_stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId("stock_id")->constrained()->cascadeOnDelete();
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->boolean("status");
            $table->bigInteger("quantity");
            $table->boolean("featured")->default("0");
            $table->boolean("special_offer")->default("0");
            $table->decimal("price");
            $table->date("expiry_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supermarkets_stock_prices');
    }
};
