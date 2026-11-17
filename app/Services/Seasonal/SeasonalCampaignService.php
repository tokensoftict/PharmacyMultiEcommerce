<?php

namespace App\Services\Seasonal;

use App\Http\Resources\Api\SeasonalCampaignResource;
use App\Models\SeasonalCampaign;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves which seasonal campaign (if any) is live and caches the public payload.
 *
 * Resolution rules
 *  1. The master switch must be ON.
 *  2. The campaign must be `enabled` and inside its start/end window (evaluated in the campaign's timezone).
 *  3. When several campaigns overlap, highest `priority` wins; ties go to the most recently
 *     started campaign, then to the newest record.
 *
 * Caching: the payload is cached until the earliest upcoming schedule boundary (a campaign
 * starting or expiring) or MAX_TTL seconds, whichever is sooner, so scheduling is automatic and
 * stays accurate. Any admin write flushes the cache via the model events and the master switch.
 */
class SeasonalCampaignService
{
    public const CACHE_KEY = 'seasonal_theme:payload';
    public const MASTER_KEY = 'seasonal_theme:master_enabled';
    public const MAX_TTL = 300;

    // ── Master switch ───────────────────────────────────────────────────────

    public function isMasterEnabled(): bool
    {
        return (bool) Cache::get(self::MASTER_KEY, true);
    }

    public function setMasterEnabled(bool $enabled): void
    {
        Cache::forever(self::MASTER_KEY, $enabled);
        $this->flushCache();
    }

    // ── Resolution ──────────────────────────────────────────────────────────

    /**
     * The campaign that should be live at $now, or null.
     */
    public function resolveActive(?CarbonImmutable $now = null): ?SeasonalCampaign
    {
        $now ??= CarbonImmutable::now();

        if (! $this->isMasterEnabled()) {
            return null;
        }

        return SeasonalCampaign::query()
            ->enabled()
            ->get()
            ->filter(fn (SeasonalCampaign $c) => $c->isScheduledAt($now))
            ->sort(function (SeasonalCampaign $a, SeasonalCampaign $b) {
                return [$b->priority, $b->startsAt()?->getTimestamp() ?? 0, $b->id]
                    <=> [$a->priority, $a->startsAt()?->getTimestamp() ?? 0, $a->id];
            })
            ->first();
    }

    /**
     * Public configuration payload (cached). Always returns a complete array;
     * when nothing is active it is the disabled configuration.
     */
    public function getPublicConfig(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $now = CarbonImmutable::now();
            $active = $this->resolveActive($now);
            $payload = $active
                ? (new SeasonalCampaignResource($active))->resolve()
                : SeasonalCampaignResource::disabled();

            Cache::put(self::CACHE_KEY, $payload, $this->ttlFor($now));

            return $payload;
        } catch (Throwable $e) {
            // Seasonal visuals must never take the API down.
            Log::warning('[SeasonalCampaignService] Falling back to disabled config: ' . $e->getMessage());

            return SeasonalCampaignResource::disabled();
        }
    }

    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Seconds until the next schedule boundary (never more than MAX_TTL, never less than 1).
     */
    protected function ttlFor(CarbonImmutable $now): int
    {
        $ttl = self::MAX_TTL;

        SeasonalCampaign::query()->enabled()->get()->each(function (SeasonalCampaign $c) use ($now, &$ttl) {
            foreach ([$c->startsAt(), $c->endsAt()?->addSecond()] as $boundary) {
                if ($boundary && $boundary->greaterThan($now)) {
                    $ttl = min($ttl, max(1, (int) $now->diffInSeconds($boundary, true)));
                }
            }
        });

        return max(1, $ttl);
    }
}

