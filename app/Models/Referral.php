<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Referral
 *
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_user_id
 * @property string $referral_code
 * @property string $store_type          — 'supermarket' | 'wholesales'
 * @property string $status              — 'pending' | 'registered' | 'verified' | 'store_approved' | 'rewarded' | 'invalid'
 * @property Carbon|null $phone_verified_at
 * @property Carbon|null $store_approved_at
 * @property Carbon|null $rewarded_at
 * @property float|null $reward_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $referrer
 * @property User $referredUser
 *
 * @package App\Models
 */
class Referral extends Model
{
    // ─── Status Constants ────────────────────────────────────────────────────
    const STATUS_PENDING        = 'pending';
    const STATUS_REGISTERED     = 'registered';
    const STATUS_VERIFIED       = 'verified';
    const STATUS_STORE_APPROVED = 'store_approved'; // Wholesale only — store approved; reward pending
    const STATUS_REWARDED       = 'rewarded';
    const STATUS_INVALID        = 'invalid';

    // ─── Store Type Constants ────────────────────────────────────────────────
    const STORE_SUPERMARKET = 'supermarket';
    const STORE_WHOLESALES  = 'wholesales';

    protected $table = 'referrals';

    protected $casts = [
        'referrer_id'       => 'int',
        'referred_user_id'  => 'int',
        'reward_amount'     => 'float',
        'phone_verified_at' => 'datetime',
        'store_approved_at' => 'datetime',
        'rewarded_at'       => 'datetime',
    ];

    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'referral_code',
        'store_type',
        'status',
        'phone_verified_at',
        'store_approved_at',
        'rewarded_at',
        'reward_amount',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    /**
     * The user who shared the referral link.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * The new user who used the referral link.
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Whether this referral has already been rewarded.
     */
    public function isRewarded(): bool
    {
        return $this->status === self::STATUS_REWARDED;
    }

    /**
     * Whether this referral is in a state that can be rewarded.
     *
     * For retail ('supermarket'), the reward fires right after phone verification.
     * For wholesale, the reward fires after store approval (STATUS_STORE_APPROVED).
     */
    public function isRewardable(): bool
    {
        if ($this->store_type === self::STORE_WHOLESALES) {
            // Wholesale referrals are only rewardable once the store is approved
            return $this->status === self::STATUS_STORE_APPROVED;
        }

        // Retail referrals are rewardable once registered or verified
        return in_array($this->status, [
            self::STATUS_REGISTERED,
            self::STATUS_VERIFIED,
        ]);
    }

    /**
     * Whether this is a wholesale referral that has been phone-verified
     * but is still waiting for store approval before the reward is issued.
     */
    public function isAwaitingStoreApproval(): bool
    {
        return $this->store_type === self::STORE_WHOLESALES
            && in_array($this->status, [self::STATUS_REGISTERED, self::STATUS_VERIFIED]);
    }

    /**
     * Returns the loyalty points column to credit on the referrer,
     * based on which store type the referred user registered under.
     *
     * 'supermarket' → retail_loyalty_points (Retail Referral Wallet)
     * 'wholesales'  → loyalty_points         (Wholesale Referral Wallet)
     */
    public static function loyaltyColumnForStoreType(string $storeType): string
    {
        return $storeType === self::STORE_SUPERMARKET
            ? 'retail_loyalty_points'
            : 'loyalty_points';
    }

    /**
     * Returns the settings key for the referral bonus,
     * based on which store type the referred user registered under.
     */
    public static function bonusSettingKeyForStoreType(string $storeType): string
    {
        return $storeType === self::STORE_SUPERMARKET
            ? 'retail_referral_bonus'
            : 'wholesale_referral_bonus';
    }
}
