<?php

namespace App\Services\Campaign;

use App\Enums\CampaignActivityType;
use App\Models\Campaign;
use App\Models\CampaignActivity;
use App\Models\SupermarketUser;
use App\Models\User;
use App\Models\WholesalesUser;
use App\Notifications\CampaignPushNotification;
use Illuminate\Support\Facades\Log;

/**
 * CampaignPushService
 *
 * Handles FCM push notification delivery for campaigns.
 * Reuses the existing FCM notification channel pattern (NotificationChannels\Fcm).
 */
class CampaignPushService
{
    /**
     * Send a campaign push notification to a single user.
     * The user must have a device_key set on their store-specific profile.
     */
    public function sendToUser(Campaign $campaign, User $user): void
    {
        $deviceKey = $this->resolveDeviceKey($user);

        if (!$deviceKey) {
            $this->logActivity($campaign, $user, CampaignActivityType::PUSH_FAILED, [
                'reason' => 'no_device_key',
            ]);
            return;
        }

        $this->logActivity($campaign, $user, CampaignActivityType::PUSH_SCHEDULED, [
            'device_key' => substr($deviceKey, 0, 20) . '...',
        ]);

        // Use the notifiable pattern: send to whichever store profile has the device_key
        $notifiable = $this->resolveNotifiable($user);
        if (!$notifiable) return;

        try {
            $notifiable->notify(new CampaignPushNotification($campaign, $user));

            $campaign->increment('total_push_sent');

            $this->logActivity($campaign, $user, CampaignActivityType::PUSH_SENT, [
                'channel' => 'push',
                'sent_at' => now()->toISOString(),
            ]);
        } catch (\Throwable $e) {
            Log::error("[CampaignPushService] FCM push failed for campaign #{$campaign->id}, user #{$user->id}: {$e->getMessage()}");

            $this->logActivity($campaign, $user, CampaignActivityType::PUSH_FAILED, [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send a campaign push to a bulk list of users (chunked for memory efficiency).
     */
    public function sendToAudience(Campaign $campaign, iterable $users, int $chunkSize = 50): void
    {
        $count = 0;
        foreach ($users as $user) {
            $this->sendToUser($campaign, $user);
            $count++;
            if ($count % $chunkSize === 0) {
                // Allow queue breathing room
                usleep(100_000); // 100ms pause every chunk
            }
        }
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function resolveDeviceKey(User $user): ?string
    {
        return $user->supermarket_users->first()?->device_key
            ?? $user->wholesales_users->first()?->device_key;
    }

    private function resolveNotifiable(User $user): mixed
    {
        // Use whichever profile has a device key
        if ($key = $user->supermarket_users->first()?->device_key) {
            return $user->supermarket_users->first();
        }
        if ($key = $user->wholesales_users->first()?->device_key) {
            return $user->wholesales_users->first();
        }
        return null;
    }

    private function logActivity(Campaign $campaign, User $user, string $eventType, array $meta = []): void
    {
        CampaignActivity::create([
            'campaign_id' => $campaign->id,
            'user_id'     => $user->id,
            'event_type'  => $eventType,
            'channel'     => 'push',
            'metadata'    => $meta,
        ]);
    }
}
