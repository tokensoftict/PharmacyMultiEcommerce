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
        Schema::create('med_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId("stock_id")->nullable()->constrained();
            $table->string('drug_name', 100)->nullable();
            $table->decimal('dosage');
            $table->decimal('total_dosage_in_package');
            $table->decimal('total_dosage_taken')->default(0);
            $table->json('normal_schedules')->nullable();
            $table->string('type', 50)->default('ONE-OFF'); // ONE-OFF, CONTINUES
            $table->boolean('use_interval')->default(false);
            $table->integer('interval')->nullable();
            $table->string('every')->nullable(); //minutes, hours, days, weeks, months
            $table->dateTime('start_date_time')->nullable();
            $table->date('date_create');
            $table->string("notes")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_reminders');
    }
};
