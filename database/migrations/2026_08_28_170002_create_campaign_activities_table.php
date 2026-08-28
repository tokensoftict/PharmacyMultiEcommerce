<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // campaign_activities: per-user per-campaign interaction tracking
        Schema::create('campaign_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('campaign_step_id')->nullable()->constrained('campaign_steps')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Event type: impression | dismissed | clicked | converted |
            //             push_scheduled | push_sent | push_failed | push_opened
            $table->string('event_type')->index();

            // Delivery channel for this activity: in_app | push
            $table->string('channel')->nullable();

            // Device info
            $table->string('device_token')->nullable();
            $table->string('platform')->nullable(); // ios | android

            // Session ID for once_per_session frequency rule
            $table->string('session_id')->nullable()->index();

            // Scheduling timestamps
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            // Attribution — which step/notification led to conversion
            $table->string('attributed_to')->nullable(); // push | in_app

            // Metadata — flexible extra data (order_id, product_id, etc)
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes for eligibility checks
            $table->index(['campaign_id', 'user_id', 'event_type']);
            $table->index(['campaign_id', 'user_id']);
            $table->index(['user_id', 'event_type']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_activities');
    }
};
