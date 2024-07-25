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
        Schema::create('product_banners', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 50);
            $table->string('image', 150);
            $table->boolean('status')->default('1');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreignId('classification_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('productgroup_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manufacturer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_banners');
    }
};
