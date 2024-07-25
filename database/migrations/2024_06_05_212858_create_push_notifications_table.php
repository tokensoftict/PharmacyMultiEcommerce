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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->text("title");
            $table->text("body");
            $table->json("payload")->nullable();
            $table->json("device_ids")->nullable();
            $table->foreignId("app_id")->constrained()->cascadeOnDelete();
            $table->bigInteger("no_of_device")->nullable();
            $table->string("action");
            $table->string("type")->default('topic');
            $table->bigInteger("total_view")->default(0);
            $table->bigInteger("total_sent")->default(0);
            $table->enum("status",['DRAFT', 'SENT', 'APPROVED', 'CANCEL'])->default("DRAFT");
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
