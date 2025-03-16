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
        Schema::create('apps', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->integer("model_id")->nullable();
            $table->string('description')->nullable();
            $table->string('logo', 100)->nullable();
            $table->boolean('show')->default(false);
            $table->string('domain', 50);
            $table->string('link', 100);
            $table->string('type', 20)->comment("TYPE OF APPS");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
