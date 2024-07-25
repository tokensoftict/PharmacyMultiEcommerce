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
        Schema::create('productgroups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string("name",100)->index();
            $table->string('seo')->nullable()->index();
            $table->boolean('status')->default("1");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productgroups');
    }
};
