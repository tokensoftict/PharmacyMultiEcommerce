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
        Schema::create('customer_search_histories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->nullableMorphs("customer");
            $table->string('keyword');
            $table->foreignId("productcategory_id")->nullable()->constrained()->nullOnDelete();
            $table->ipAddress('ip');
            $table->date('date_added');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_search_histories');
    }
};
