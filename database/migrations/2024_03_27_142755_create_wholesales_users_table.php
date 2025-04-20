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
        Schema::create('wholesales_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->boolean('status')->default(0);
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('business_name')->nullable();
            $table->foreignId('customer_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_key')->nullable();
            $table->string('cac_document')->nullable();
            $table->string('premises_licence')->nullable();
            $table->unsignedBigInteger('customer_local_id')->nullable();
            $table->string('phone',20)->nullable();
            $table->unsignedBigInteger('sales_representative_id')->nullable();
            $table->json('cart')->nullable();
            $table->json('wishlist')->nullable();
            $table->json('checkout')->nullable();
            $table->dateTime('last_activity_date')->nullable();
            $table->json('ordertotals')->nullable();
            $table->json('coupon_data')->nullable();
            $table->json('remove_order_total')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesales_users');
    }
};
