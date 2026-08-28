<?php

namespace App\Services\Campaign;

use App\Enums\CampaignActivityType;
use App\Models\Campaign;
use App\Models\CampaignActivity;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

/**
 * CampaignDeliveryService
 *
 * Formats campaigns into the API response payload consumed by React Native
 * and delegates push notification delivery to the push notification service.
 */
class CampaignDeliveryService
{
    public function __construct(
        private readonly CampaignPushService $pushService
    ) {}

    /**
     * Format a collection of eligible campaigns for API delivery (in-app rendering).
     */
    public function formatForApi(iterable $campaigns, int $userId, string $sessionId = ''): array
    {
        $formatted = [];

        foreach ($campaigns as $campaign) {
            // Only include campaigns with in-app delivery
            if (!in_array($campaign->delivery_channel, ['in_app', 'both'])) continue;

            // Record impression
            CampaignActivity::logImpression($campaign, $userId, 'in_app', [
                'session_id' => $sessionId,
            ]);

            $formatted[] = $this->buildCampaignPayload($campaign);
        }

        return $formatted;
    }

    /**
     * Send push notifications for a set of eligible campaigns to a user.
     */
    public function dispatchPushNotifications(iterable $campaigns, User $user): void
    {
        foreach ($campaigns as $campaign) {
            if (!in_array($campaign->delivery_channel, ['push', 'both'])) continue;

            try {
                $this->pushService->sendToUser($campaign, $user);
            } catch (\Throwable $e) {
                Log::error("[CampaignDelivery] Push failed for campaign #{$campaign->id}, user #{$user->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Build the serialisable payload for a single campaign — consumed by React Native.
     */
    public function buildCampaignPayload(Campaign $campaign): array
    {
        return [
            'id'              => $campaign->id,
            'slug'            => $campaign->slug,
            'title'           => $campaign->title,
            'message'         => $campaign->message,
            'image'           => $campaign->image ? asset('storage/' . $campaign->image) : null,
            'cta_text'        => $campaign->cta_text,
            'display_type'    => $campaign->display_type,   // modal|fullscreen|bottom_sheet|banner
            'action_type'     => $campaign->action_type,
            'action_data'     => $campaign->action_data ?? [],
            'trigger_event'   => $campaign->trigger_event,
        ];
    }

    /**
     * Record an interaction from the mobile app.
     */
    public function recordInteraction(int $campaignId, int $userId, string $eventType, string $channel, array $meta = []): void
    {
        $campaign = Campaign::find($campaignId);
        if (!$campaign) return;

        match ($eventType) {
            CampaignActivityType::CLICKED    => CampaignActivity::logClick($campaign, $userId, $channel, $meta),
            CampaignActivityType::DISMISSED  => CampaignActivity::logDismissal($campaign, $userId, $channel, $meta),
            CampaignActivityType::CONVERTED  => CampaignActivity::logConversion($campaign, $userId, $channel, $meta),
            default => CampaignActivity::create(array_merge([
                'campaign_id' => $campaignId,
                'user_id'     => $userId,
                'event_type'  => $eventType,
                'channel'     => $channel,
                'metadata'    => $meta,
            ])),
        };
    }
}
