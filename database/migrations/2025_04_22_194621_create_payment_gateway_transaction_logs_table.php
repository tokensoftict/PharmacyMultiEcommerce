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
        Schema::create('payment_gateway_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference', 150);
            $table->string('gateway', 100);
            $table->string('status', 20);
            $table->decimal('total', 15, 5);
            $table->string("email", 50);
            $table->string("phone", 20);
            $table->string("currency", 10);
            $table->foreignId("user_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_transaction_logs');
    }
};
