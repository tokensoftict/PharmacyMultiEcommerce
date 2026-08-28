<?php

namespace App\Services\Referral;

use App\Classes\Settings;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ReferralService
 *
 * Handles the complete referral lifecycle:
 *   1. createReferralRelationship() — called at registration
 *   2. processReferralReward()      — called after phone verification (idempotent)
 *   3. getReferralStats()           — called by the referral stats API endpoint
 *
 * WALLET ARCHITECTURE
 * -------------------
 * There is no separate wallet table in this application.
 * The reward currency is Loyalty Points:
 *   store_type = 'supermarket'  →  credits users.retail_loyalty_points
 *   store_type = 'wholesales'   →  credits users.loyalty_points
 *
 * BONUS AMOUNT SOURCE
 * -------------------
 * Amounts come from the Store Settings (Settings::getSetting()):
 *   'retail_referral_bonus'    — retail/supermarket referral bonus
 *   'wholesale_referral_bonus' — wholesale referral bonus
 */
class ReferralService
{
    /**
     * Establish a referral relationship when a new user registers.
     *
     * Called immediately after the user account (and store account) is created.
     * Does NOT award any bonus — only creates the tracking record.
     *
     * @param User   $referredUser  The newly registered user
     * @param string $referralCode  The code the user provided at registration
     * @param string $storeType     'supermarket' | 'wholesales'
     * @return Referral|null
     */
    public function createReferralRelationship(
        User $referredUser,
        string $referralCode,
        string $storeType
    ): ?Referral {
        // ── Validate store type ──────────────────────────────────────────────
        if (!in_array($storeType, [Referral::STORE_SUPERMARKET, Referral::STORE_WHOLESALES])) {
            Log::warning('ReferralService: invalid store_type', ['store_type' => $storeType]);
            return null;
        }

        // ── Find the referrer by code ────────────────────────────────────────
        $referrer = User::where('referral_code', $referralCode)->first();

        if (!$referrer) {
            // Invalid code — silently ignore (not a hard error for registration)
            Log::info('ReferralService: referral code not found', ['code' => $referralCode]);
            return null;
        }

        // ── Prevent self-referral ────────────────────────────────────────────
        if ($referrer->id === $referredUser->id) {
            Log::info('ReferralService: self-referral attempted', ['user_id' => $referredUser->id]);
            return null;
        }

        // ── Prevent duplicate referral for this referred user ────────────────
        $existingReferral = Referral::where('referred_user_id', $referredUser->id)->first();
        if ($existingReferral) {
            Log::info('ReferralService: referred user already has a referral', [
                'referred_user_id' => $referredUser->id,
            ]);
            return $existingReferral;
        }

        // ── Create the referral record ───────────────────────────────────────
        $referral = Referral::create([
            'referrer_id'      => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'referral_code'    => $referralCode,
            'store_type'       => $storeType,
            'status'           => Referral::STATUS_REGISTERED,
        ]);

        Log::info('ReferralService: referral relationship created', [
            'referral_id'      => $referral->id,
            'referrer_id'      => $referrer->id,
            'referred_user_id' => $referredUser->id,
            'store_type'       => $storeType,
        ]);

        return $referral;
    }

    /**
     * Process the referral reward after the referred user successfully verifies their phone.
     *
     * IDEMPOTENT — safe to call multiple times; will never double-credit.
     * Runs inside a database transaction with a row-level lock.
     *
     * @param User $referredUser  The user whose phone was just verified
     */
    public function processReferralReward(User $referredUser): void
    {
        DB::transaction(function () use ($referredUser) {

            // ── Load and lock the referral record ────────────────────────────────────
            $referral = Referral::where('referred_user_id', $referredUser->id)
                ->lockForUpdate()
                ->first();

            if (!$referral) {
                // No referral record — nothing to reward
                return;
            }

            // ── Idempotency guard ─────────────────────────────────────────────────
            if ($referral->isRewarded()) {
                Log::info('ReferralService: referral already rewarded — skipping', [
                    'referral_id' => $referral->id,
                ]);
                return;
            }

            // ── WHOLESALE: phone verification only advances to 'verified'; reward fires on store approval ──
            if ($referral->store_type === Referral::STORE_WHOLESALES) {
                if (in_array($referral->status, [Referral::STATUS_REGISTERED, Referral::STATUS_PENDING])) {
                    $referral->update([
                        'status'           => Referral::STATUS_VERIFIED,
                        'phone_verified_at' => now(),
                    ]);
                    Log::info('ReferralService: wholesale referral advanced to verified — awaiting store approval', [
                        'referral_id' => $referral->id,
                    ]);
                }
                return; // Reward will be issued in processWholesaleApprovalReward()
            }

            // ── RETAIL: validate the referral is in a rewardable state ───────────────────────
            if (!$referral->isRewardable()) {
                Log::warning('ReferralService: referral not in rewardable state', [
                    'referral_id' => $referral->id,
                    'status'      => $referral->status,
                ]);
                return;
            }

            $this->issueReward($referral);
        });
    }

    /**
     * Process the wholesale referral reward after the referred user's store is approved.
     *
     * Called from WholeSalesCustomerService::activateBusiness() when status flips to true.
     * IDEMPOTENT — safe to call multiple times; will never double-credit.
     *
     * @param User $referredUser  The wholesale customer whose store was just approved
     */
    public function processWholesaleApprovalReward(User $referredUser): void
    {
        DB::transaction(function () use ($referredUser) {

            $referral = Referral::where('referred_user_id', $referredUser->id)
                ->where('store_type', Referral::STORE_WHOLESALES)
                ->lockForUpdate()
                ->first();

            if (!$referral) {
                return;
            }

            if ($referral->isRewarded()) {
                Log::info('ReferralService: wholesale referral already rewarded — skipping', [
                    'referral_id' => $referral->id,
                ]);
                return;
            }

            // Advance to store_approved if the store was verified but not yet approved
            if ($referral->isAwaitingStoreApproval()) {
                $referral->update([
                    'status'            => Referral::STATUS_STORE_APPROVED,
                    'store_approved_at' => now(),
                ]);
                $referral->refresh();
            }

            if (!$referral->isRewardable()) {
                Log::warning('ReferralService: wholesale referral not rewardable after store activation', [
                    'referral_id' => $referral->id,
                    'status'      => $referral->status,
                ]);
                return;
            }

            $this->issueReward($referral);
        });
    }

    /**
     * Issue the actual loyalty point reward to the referrer.
     *
     * Must be called from within a DB transaction with the referral row already locked.
     * Marks the referral as STATUS_REWARDED after crediting.
     *
     * @param Referral $referral  Locked referral record in a rewardable state
     */
    private function issueReward(Referral $referral): void
    {
        // ── Validate referrer still exists ──────────────────────────────────
        $referrer = User::lockForUpdate()->find($referral->referrer_id);
        if (!$referrer) {
            $referral->update(['status' => Referral::STATUS_INVALID]);
            return;
        }

        // ── Determine bonus from store settings ──────────────────────────────
        $settings   = Settings::getSetting();
        $settingKey = Referral::bonusSettingKeyForStoreType($referral->store_type);
        $bonus      = (float) ($settings->get($settingKey) ?? 0);

        if ($bonus <= 0) {
            Log::warning('ReferralService: referral bonus is zero or not configured', [
                'setting_key' => $settingKey,
                'store_type'  => $referral->store_type,
            ]);
            // Still mark as rewarded with 0 to prevent infinite retry loops
        }

        // ── Credit the referrer's loyalty points ─────────────────────────────
        $loyaltyColumn = Referral::loyaltyColumnForStoreType($referral->store_type);
        if ($bonus > 0) {
            User::where('id', $referrer->id)->increment($loyaltyColumn, $bonus);
        }

        // ── Mark the referral as rewarded ────────────────────────────────────
        $referral->update([
            'status'        => Referral::STATUS_REWARDED,
            'rewarded_at'   => now(),
            'reward_amount' => $bonus,
        ]);

        Log::info('ReferralService: referral reward processed', [
            'referral_id'    => $referral->id,
            'referrer_id'    => $referrer->id,
            'store_type'     => $referral->store_type,
            'loyalty_column' => $loyaltyColumn,
            'bonus'          => $bonus,
        ]);
    }

    /**
     * Get referral statistics for a user, broken down by store type.
     *
     * @param User $user
     * @return array
     */
    public function getReferralStats(User $user): array
    {
        $referralCode = $user->getReferralCode();

        $stats = Referral::where('referrer_id', $user->id)
            ->selectRaw("
                store_type,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'rewarded' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status IN ('registered','verified','store_approved') THEN 1 ELSE 0 END) as pending,
                COALESCE(SUM(CASE WHEN status = 'rewarded' THEN reward_amount ELSE 0 END), 0) as bonus_earned
            ")
            ->groupBy('store_type')
            ->get()
            ->keyBy('store_type');

        $baseUrl = rtrim(config('app.referral_url', 'https://referral.generaldrugcentre.com'), '/');

        return [
            'referral_code' => $referralCode,
            'referral_url'  => "{$baseUrl}/ref/{$referralCode}",
            'supermarket'   => [
                'successful_referrals' => (int) ($stats->get('supermarket')?->successful ?? 0),
                'pending_referrals'    => (int) ($stats->get('supermarket')?->pending    ?? 0),
                'bonus_earned'         => (float) ($stats->get('supermarket')?->bonus_earned ?? 0),
            ],
            'wholesales'    => [
                'successful_referrals' => (int) ($stats->get('wholesales')?->successful ?? 0),
                'pending_referrals'    => (int) ($stats->get('wholesales')?->pending    ?? 0),
                'bonus_earned'         => (float) ($stats->get('wholesales')?->bonus_earned ?? 0),
            ],
        ];
    }
}
