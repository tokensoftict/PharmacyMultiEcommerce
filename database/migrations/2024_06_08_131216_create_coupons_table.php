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
        Schema::create('coupons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->integer('noofuse')->default(0);
            $table->enum('type', ['Percentage', 'Fixed']);
            $table->decimal('type_value');
            $table->foreignId("app_id")->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs("users");
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
        Schema::dropIfExists('coupons');
    }
};
