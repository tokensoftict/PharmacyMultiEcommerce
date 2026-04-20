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
        Schema::create('local_invoices', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->unsignedBigInteger('invoice_id')->index();
            $blueprint->unsignedBigInteger('customer_id')->nullable()->index();
            $blueprint->unsignedBigInteger('user_id')->nullable()->index();
            $blueprint->unsignedBigInteger('app_id')->nullable()->index();
            $blueprint->string('department')->nullable();
            $blueprint->date('invoice_date')->nullable();
            $blueprint->string('customer_phone_number')->nullable();
            $blueprint->decimal('total', 15, 2)->nullable();
            $blueprint->string('payment_methods')->nullable();
            $blueprint->json('data');
            $blueprint->timestamps();

            $blueprint->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $blueprint->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_invoices');
    }
};
