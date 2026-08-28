<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Status: draft | active | paused | expired | archived
            $table->string('status')->default('draft')->index();

            // Priority — higher = shown first
            $table->unsignedInteger('priority')->default(0)->index();

            // Schedule
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();

            // Store targeting: retail | wholesale | both
            $table->string('store_type')->default('both')->index();

            // Audience targeting: all_users | new_users | existing_users | customer_group | specific_customers
            $table->string('audience_type')->default('all_users');
            $table->json('audience_ids')->nullable(); // customer group ids or specific user ids

            // Trigger event (e.g. APP_OPEN, ADD_TO_CART, CART_ABANDONED)
            $table->string('trigger_event')->nullable()->index();

            // Conditions — flexible JSON condition tree
            $table->json('conditions')->nullable();

            // Delivery channels: in_app | push | both
            $table->string('delivery_channel')->default('both');

            // Display type for in-app: modal | fullscreen | bottom_sheet | banner
            $table->string('display_type')->default('modal');

            // Frequency rules
            $table->string('frequency_rule')->default('once_ever');
            // once_ever | once_per_session | once_per_login | once_per_day | cooldown | max_times | unlimited
            $table->unsignedInteger('max_impressions')->nullable(); // global cap
            $table->unsignedInteger('max_impressions_per_user')->nullable(); // per-user cap
            $table->unsignedInteger('max_clicks')->nullable();
            $table->unsignedInteger('cooldown_minutes')->nullable(); // minimum gap between shows

            // Random delivery
            $table->unsignedTinyInteger('random_probability')->nullable(); // 0–100

            // Campaign image
            $table->string('image')->nullable();

            // In-app content
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('cta_text')->nullable();

            // Push notification content
            $table->string('push_title')->nullable();
            $table->text('push_body')->nullable();

            // Click action
            $table->string('action_type')->default('none');
            // none | open_product | open_category | open_cart | open_checkout | open_order | open_store | open_url | open_deep_link | apply_coupon
            $table->json('action_data')->nullable();

            // Totals (denormalised for fast queries)
            $table->unsignedBigInteger('total_impressions')->default(0);
            $table->unsignedBigInteger('total_clicks')->default(0);
            $table->unsignedBigInteger('total_dismissals')->default(0);
            $table->unsignedBigInteger('total_conversions')->default(0);
            $table->unsignedBigInteger('total_push_sent')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
