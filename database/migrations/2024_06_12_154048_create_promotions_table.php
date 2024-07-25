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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name',255)->nullable();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->date('from_date');
            $table->date('end_date');
            $table->date('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
