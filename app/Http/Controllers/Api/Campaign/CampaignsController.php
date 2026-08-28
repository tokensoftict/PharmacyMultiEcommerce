<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\ApiController;
use App\Services\Campaign\CampaignConditionsService;
use App\Services\Campaign\CampaignDeliveryService;
use App\Services\Campaign\CampaignEligibilityService;
use App\Services\Campaign\CampaignPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CampaignsController
 *
 * Returns eligible in-app campaigns for a given trigger event and store type.
 * Called by the React Native app on:
 *   - App open / foreground
 *   - Navigation events
 *   - Cart changes
 *   - Login/signup
 */
class CampaignsController extends ApiController
{
    public function __construct(
        private readonly CampaignEligibilityService $eligibility,
        private readonly CampaignDeliveryService    $delivery,
    ) {}

    /**
     * GET /campaign/eligible?trigger=APP_OPEN&store_type=supermarket
     *
     * Returns campaigns eligible for in-app display.
     * Immediately records impressions.
     */
    public function eligible(Request $request): JsonResponse
    {
        $user      = $request->user();
        $trigger   = $request->get('trigger', 'APP_OPEN');
        $storeType = $request->get('store_type', 'supermarket');
        $sessionId = $request->get('session_id', '');

        $context = [
            'store_type'      => $storeType,
            'session_id'      => $sessionId,
            'cart_total'      => (float) $request->get('cart_total', 0),
            'cart_item_count' => (int)   $request->get('cart_item_count', 0),
            'platform'        => $request->get('platform'),
            'app_version'     => $request->get('app_version'),
        ];

        $campaigns = $this->eligibility->resolveEligible($user, $trigger, $storeType, $context);

        // Filter to in-app delivery only (push campaigns are handled server-side)
        $inAppCampaigns = $campaigns->filter(fn($c) => in_array($c->delivery_channel, ['in_app', 'both']));

        $payload = $this->delivery->formatForApi($inAppCampaigns, $user->id, $sessionId);

        return $this->sendSuccessResponse($payload);
    }
}
