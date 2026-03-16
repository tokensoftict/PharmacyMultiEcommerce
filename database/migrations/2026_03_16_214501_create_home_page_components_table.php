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
        Schema::create('home_page_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->string('component_name'); // e.g., Horizontal_List, ImageSlider, topBrands, FlashDeals
            $table->string('type');           // e.g., classifications, manufacturers, topBrands, lowestClassifications, ImageSlider, specialOffers, new_arrivals
            $table->string('component_id')->nullable(); // ID of the classification or manufacturer
            $table->string('label')->nullable();
            $table->integer('limit')->default(15);
            $table->string('see_all_link')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_page_components');
    }
};
