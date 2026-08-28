<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds store_approved_at timestamp to referrals and extends the status enum
 * to include 'store_approved' — used for wholesale referrals where the reward
 * is gated behind admin approval of the referred user's store.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            // Nullable timestamp set when an admin approves the referred wholesale store
            $table->timestamp('store_approved_at')->nullable()->after('phone_verified_at');
        });

        // Extend the status column enum to include 'store_approved'
        DB::statement("
            ALTER TABLE referrals
            MODIFY COLUMN status
            ENUM('pending','registered','verified','store_approved','rewarded','invalid')
            NOT NULL DEFAULT 'registered'
        ");
    }

    public function down(): void
    {
        // Revert the status enum (drop 'store_approved')
        DB::statement("
            ALTER TABLE referrals
            MODIFY COLUMN status
            ENUM('pending','registered','verified','rewarded','invalid')
            NOT NULL DEFAULT 'registered'
        ");

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('store_approved_at');
        });
    }
};
