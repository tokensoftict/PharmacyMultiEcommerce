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
        Schema::create('push_notification_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('push_notification_id')->constrained('push_notifications')->cascadeOnDelete();
            $table->foreignId('stock_id');
            $table->foreignId("app_id")->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notification_stocks');
    }
};
