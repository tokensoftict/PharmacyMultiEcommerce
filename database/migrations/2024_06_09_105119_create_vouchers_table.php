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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->enum('type', ['Percentage', 'Fixed']);
            $table->decimal('type_value'); //Percentage or Fixed
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->integer('noofvoucher');
            $table->nullableMorphs("user");
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
        Schema::dropIfExists('vouchers');
    }
};
