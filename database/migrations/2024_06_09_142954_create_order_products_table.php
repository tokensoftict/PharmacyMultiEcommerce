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
        Schema::create('order_products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string("order_product_id","100")->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('local_id');
            $table->string('name');
            $table->string("error")->nullable();
            $table->string('model', 64);
            $table->integer('quantity');
            $table->decimal('price', 15, 5);
            $table->decimal('total', 15, 5);
            $table->decimal('tax', 15, 5);
            $table->integer('reward');
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_representative_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
