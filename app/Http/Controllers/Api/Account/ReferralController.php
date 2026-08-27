<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ApiController;
use App\Services\Referral\ReferralService;
use Illuminate\Http\JsonResponse;

/**
 * ReferralController
 *
 * Endpoints:
 *   GET /api/account/referral-code  — returns or generates the user's referral code
 *   GET /api/account/referrals/me   — returns referral statistics (retail + wholesale breakdown)
 */
class ReferralController extends ApiController
{
    public function __construct(private readonly ReferralService $referralService)
    {
    }

    /**
     * Return the authenticated user's referral code.
     * If the user does not yet have one, it is generated now.
     *
     * GET /api/account/referral-code
     */
    public function referralCode(): JsonResponse
    {
        $user = auth('sanctum')->user();
        $code = $user->getReferralCode();

        $baseUrl = rtrim(config('app.referral_url', 'https://referral.generaldrugcentre.com'), '/');

        return $this->sendSuccessResponse([
            'referral_code' => $code,
            'referral_url'  => "{$baseUrl}/ref/{$code}",
        ]);
    }

    /**
     * Return referral statistics for the authenticated user,
     * broken down by store type (supermarket / wholesales).
     *
     * GET /api/account/referrals/me
     */
    public function myReferrals(): JsonResponse
    {
        $user  = auth('sanctum')->user();
        $stats = $this->referralService->getReferralStats($user);

        return $this->sendSuccessResponse($stats);
    }
}
