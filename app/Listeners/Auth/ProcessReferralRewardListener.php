<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PhoneVerified;
use App\Services\Referral\ReferralService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * ProcessReferralRewardListener
 *
 * Triggered by the PhoneVerified event.
 * Delegates to ReferralService::processReferralReward() which is fully idempotent.
 * Multiple verifications for the same user will never result in multiple rewards.
 */
class ProcessReferralRewardListener implements ShouldQueue
{
    public function __construct(private readonly ReferralService $referralService)
    {
    }

    /**
     * Handle the PhoneVerified event.
     *
     * @param PhoneVerified $event
     */
    public function handle(PhoneVerified $event): void
    {
        try {
            $this->referralService->processReferralReward($event->user);
        } catch (\Throwable $e) {
            // Log but do not rethrow — referral reward failure must not block phone verification
            Log::error('ProcessReferralRewardListener: failed to process referral reward', [
                'user_id' => $event->user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }
}
