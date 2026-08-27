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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // The user who shared the referral link
            $table->foreignId('referrer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // The new user who was referred
            $table->foreignId('referred_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // The referral code that was used at registration time (snapshot)
            $table->string('referral_code', 10);

            // Which store the referred user registered under
            // 'supermarket' = retail/supermarket store
            // 'wholesales'  = wholesale store
            $table->enum('store_type', ['supermarket', 'wholesales']);

            // Lifecycle status of the referral
            $table->enum('status', ['pending', 'registered', 'verified', 'rewarded', 'invalid'])
                  ->default('pending');

            // Timestamp when the referred user's phone was verified
            $table->timestamp('phone_verified_at')->nullable();

            // Timestamp when the reward was processed
            $table->timestamp('rewarded_at')->nullable();

            // Amount credited to the referrer (from store settings at time of reward)
            $table->decimal('reward_amount', 10, 2)->nullable();

            $table->timestamps();

            // ─── Constraints ────────────────────────────────────────────────
            // A user can only be referred once
            $table->unique('referred_user_id');

            // Performance indexes
            $table->index('referrer_id');
            $table->index('referral_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
