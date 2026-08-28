<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cart_abandonment_trackers');

        // cart_abandonment_trackers: one record per user+store, updated on every cart change
        Schema::create('cart_abandonment_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // retail | wholesale
            $table->string('store_type')->default('retail');

            // Cart snapshot: item count and total at last activity time
            $table->unsignedInteger('item_count')->default(0);
            $table->decimal('cart_total', 12, 2)->default(0);

            // JSON snapshot of stock_ids and quantities so we can check stock later
            $table->json('cart_snapshot')->nullable();

            // Stock levels at the time items were added (for urgency detection)
            $table->json('stock_levels_snapshot')->nullable();

            // Timestamps
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamp('order_placed_at')->nullable();

            // Whether abandonment has been triggered for the current cart state
            $table->boolean('abandonment_triggered')->default(false)->index();

            // Total number of abandonment notifications sent for current cart
            $table->unsignedInteger('abandon_notification_count')->default(0);

            $table->timestamps();

            // One tracker per user per store
            $table->unique(['user_id', 'store_type']);
            $table->index(['store_type', 'last_activity_at', 'abandonment_triggered'], 'idx_cart_abandon_scan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_abandonment_trackers');
    }
};
