<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();

            $table->unsignedInteger('step_number')->default(1);

            // Delay from previous step (or from trigger for step 1)
            $table->unsignedInteger('delay_minutes')->default(0);

            // Delivery channel for this step: in_app | push
            $table->string('delivery_channel')->default('push');

            // Display type for in-app steps
            $table->string('display_type')->nullable();

            // Content overrides (falls back to campaign defaults if null)
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('image')->nullable();
            $table->string('cta_text')->nullable();

            // Step-level conditions (evaluated at delivery time)
            $table->json('conditions')->nullable();

            // Action
            $table->string('action_type')->default('none');
            $table->json('action_data')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(0);

            $table->timestamps();

            $table->index(['campaign_id', 'step_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_steps');
    }
};
