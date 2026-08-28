<?php

namespace App\Http\Controllers\Api\Campaign;

use App\Http\Controllers\ApiController;
use App\Services\Campaign\CampaignDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CampaignInteractionController
 *
 * Records user interactions with campaigns from React Native.
 *
 * POST /campaign/interaction
 * Body:
 *   campaign_id  : int
 *   event_type   : impression | dismissed | clicked | converted | push_opened
 *   channel      : in_app | push
 *   metadata     : object (optional — e.g. order_id for conversion events)
 */
class CampaignInteractionController extends ApiController
{
    public function __construct(
        private readonly CampaignDeliveryService $delivery
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'event_type'  => ['required', 'string'],
            'channel'     => ['required', 'string', 'in:in_app,push'],
            'metadata'    => ['nullable', 'array'],
        ]);

        $this->delivery->recordInteraction(
            (int) $request->input('campaign_id'),
            $request->user()->id,
            $request->input('event_type'),
            $request->input('channel'),
            $request->input('metadata', []),
        );

        return $this->sendSuccessMessageResponse('Interaction recorded');
    }
}
