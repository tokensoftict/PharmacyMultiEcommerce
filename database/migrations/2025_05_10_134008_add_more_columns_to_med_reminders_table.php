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
        Schema::table('med_reminders', function (Blueprint $table) {
            $table->string("dosage_form", 50)->nullable()->after("total_dosage_taken");
            $table->decimal('discount_percentage', 5, 2)->nullable()->after("dosage_form");
            $table->date('discount_generated_date')->nullable()->after("discount_percentage");
            $table->date('discount_expiry_date')->nullable()->after("discount_generated_date");
            $table->boolean("is_discount_generated")->nullable()->after("discount_expiry_date");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('med_reminders', function (Blueprint $table) {
            $table->dropColumn(["dosage_form", "discount_percentage", "discount_generated_date", "discount_expiry_date"]);
        });
    }
};
