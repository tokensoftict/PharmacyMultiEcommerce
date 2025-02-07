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
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string("order_id","100")->unique();
            $table->unsignedBigInteger('invoice_no')->default(0);
            $table->morphs("customer");
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();

            $table->string('firstname', 32);
            $table->string('lastname', 32);
            $table->string('email', 100);
            $table->string('telephone', 32);
            $table->date('order_date');
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_method_id')->constrained()->cascadeOnDelete();
            $table->text('comment')->nullable();
            $table->decimal('total', 15, 5);
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->unsignedBigInteger("payment_address_id");
            $table->unsignedBigInteger("shipping_address_id");
            $table->text('payment_gateway_response')->nullable();
            $table->json('checkout_data')->nullable();
            $table->json('ordertotals')->nullable();
            $table->bigInteger('no_of_cartons')->nullable();
            $table->string('prove_of_payment', 100)->nullable();
            $table->json('order_validation_error')->nullable();
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_representative_id')->nullable()->constrained()->nullOnDelete();
            $table->json('coupon_information')->nullable();
            $table->json('voucher_information')->nullable();
            $table->json('cart_cache')->nullable();

            $table->timestamps();

            $table->foreign('payment_address_id')->references('id')->on('addresses');
            $table->foreign('shipping_address_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
