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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->string('path');
            $table->string('code');
            $table->integer('status')->default(1);
            $table->longText('template_settings')->nullable();
            $table->json('template_settings_value')->nullable();
            $table->longText('checkout_template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
