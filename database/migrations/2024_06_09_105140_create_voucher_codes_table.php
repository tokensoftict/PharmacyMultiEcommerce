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
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['Percentage', 'Fixed']);
            $table->decimal('type_value');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->enum('usage_status', ['USED', 'NOT-USED']);
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs("customer");
            $table->foreignId('customer_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_codes');
    }
};
