<?php

namespace App\Services\Campaign;

use App\Enums\CampaignActivityType;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * CampaignEligibilityService
 *
 * Determines whether a given user is eligible to receive a specific campaign
 * at the current point in time, based on:
 *  - Campaign active status and schedule
 *  - Store type targeting
 *  - Audience targeting
 *  - Frequency / impression limits
 *  - Cooldown periods
 *  - Probability (random delivery)
 *  - Condition trees
 */
class CampaignEligibilityService
{
    public function __construct(
        private readonly CampaignConditionsService $conditionsService
    ) {}

    /**
     * Resolve campaigns eligible for a user+trigger+store combination.
     *
     * @return Collection|Campaign[]
     */
    public function resolveEligible(User $user, string $triggerEvent, string $storeType, array $context = []): Collection
    {
        $campaigns = Campaign::query()
            ->active()
            ->forStore($storeType)
            ->forTrigger($triggerEvent)
            ->orderByDesc('priority')
            ->get();

        return $campaigns->filter(fn(Campaign $campaign) =>
            $this->isEligible($campaign, $user, $context)
        )->values();
    }

    /**
     * Check whether a single campaign is eligible for the given user.
     */
    public function isEligible(Campaign $campaign, User $user, array $context = []): bool
    {
        try {
            // 1. Audience targeting
            if (!$this->passesAudienceCheck($campaign, $user)) return false;

            // 2. Global impression cap
            if ($campaign->max_impressions !== null
                && $campaign->total_impressions >= $campaign->max_impressions
            ) return false;

            // 3. Per-user impression cap
            $userImpressionCount = $this->getUserImpressionCount($campaign, $user->id);

            if ($campaign->max_impressions_per_user !== null
                && $userImpressionCount >= $campaign->max_impressions_per_user
            ) return false;

            // 4. Frequency rule
            if (!$this->passesFrequencyRule($campaign, $user, $userImpressionCount, $context)) return false;

            // 5. Random probability
            if ($campaign->random_probability !== null
                && rand(1, 100) > $campaign->random_probability
            ) return false;

            // 6. Condition tree evaluation
            if (!empty($campaign->conditions)
                && !$this->conditionsService->evaluate($campaign->conditions, $user, $context)
            ) return false;

            return true;

        } catch (\Throwable $e) {
            Log::error("[CampaignEligibility] Error checking campaign #{$campaign->id} for user #{$user->id}: {$e->getMessage()}");
            return false;
        }
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function passesAudienceCheck(Campaign $campaign, User $user): bool
    {
        return match ($campaign->audience_type) {
            'all_users' => true,
            'new_users' => $user->created_at?->gt(now()->subDays(30)),
            'existing_users' => $user->created_at?->lt(now()->subDays(30)),
            'customer_group' => $this->userInCustomerGroups($campaign, $user),
            'specific_customers' => in_array($user->id, (array) $campaign->audience_ids, true),
            default => true,
        };
    }

    private function userInCustomerGroups(Campaign $campaign, User $user): bool
    {
        if (empty($campaign->audience_ids)) return true;

        // Check supermarket and wholesales user membership
        $supMemberGroupId = $user->supermarket_users->first()?->member_group_id;
        $wsMemberGroupId  = $user->wholesales_users->first()?->member_group_id;

        return in_array($supMemberGroupId, (array) $campaign->audience_ids, true)
            || in_array($wsMemberGroupId, (array) $campaign->audience_ids, true);
    }

    private function getUserImpressionCount(Campaign $campaign, int $userId): int
    {
        return CampaignActivity::where('campaign_id', $campaign->id)
            ->where('user_id', $userId)
            ->whereIn('event_type', [CampaignActivityType::IMPRESSION, CampaignActivityType::PUSH_SENT])
            ->count();
    }

    private function passesFrequencyRule(Campaign $campaign, User $user, int $impressionCount, array $context): bool
    {
        return match ($campaign->frequency_rule) {
            'once_ever'       => $impressionCount === 0,
            'once_per_login'  => $this->noImpressionInSession($campaign, $user->id, $context['session_id'] ?? null),
            'once_per_session' => $this->noImpressionInSession($campaign, $user->id, $context['session_id'] ?? null),
            'once_per_day'    => $this->noImpressionToday($campaign, $user->id),
            'max_times'       => $impressionCount < ($campaign->max_impressions_per_user ?? 999),
            'cooldown'        => $this->cooldownExpired($campaign, $user->id),
            'unlimited'       => true,
            default           => true,
        };
    }

    private function noImpressionInSession(Campaign $campaign, int $userId, ?string $sessionId): bool
    {
        if (!$sessionId) return true;

        return !CampaignActivity::where('campaign_id', $campaign->id)
            ->where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->whereIn('event_type', [CampaignActivityType::IMPRESSION, CampaignActivityType::PUSH_SENT])
            ->exists();
    }

    private function noImpressionToday(Campaign $campaign, int $userId): bool
    {
        return !CampaignActivity::where('campaign_id', $campaign->id)
            ->where('user_id', $userId)
            ->whereIn('event_type', [CampaignActivityType::IMPRESSION, CampaignActivityType::PUSH_SENT])
            ->whereDate('created_at', today())
            ->exists();
    }

    private function cooldownExpired(Campaign $campaign, int $userId): bool
    {
        if (!$campaign->cooldown_minutes) return true;

        $lastActivity = CampaignActivity::where('campaign_id', $campaign->id)
            ->where('user_id', $userId)
            ->whereIn('event_type', [CampaignActivityType::IMPRESSION, CampaignActivityType::PUSH_SENT])
            ->latest()
            ->value('created_at');

        if (!$lastActivity) return true;

        return $lastActivity->addMinutes($campaign->cooldown_minutes)->isPast();
    }
}
