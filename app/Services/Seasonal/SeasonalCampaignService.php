<?php

namespace App\Services\Seasonal;

use App\Http\Resources\Api\SeasonalCampaignResource;
use App\Models\SeasonalCampaign;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Resolves which seasonal campaign (if any) is live for a given user (or guest) and caches the payload.
 *
 * Resolution rules
 *  1. The master switch must be ON.
 *  2. The campaign must be `enabled`, inside its start/end window (in the campaign's timezone),
 *     and match the audience (`all_users` or `specific_users` containing `$userId`).
 *  3. When several campaigns overlap, highest `priority` wins; ties prefer `specific_users`
 *     campaigns over `all_users`, then the most recently started campaign, then the newest record.
 */
class SeasonalCampaignService
{
    public const CACHE_KEY = 'seasonal_theme:payload';
    public const VERSION_KEY = 'seasonal_theme:version';
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
     * The campaign that should be live at $now for $userId (or guest when null), or null.
     */
    public function resolveActive(?CarbonImmutable $now = null, ?int $userId = null): ?SeasonalCampaign
    {
        $now ??= CarbonImmutable::now();

        if (! $this->isMasterEnabled()) {
            return null;
        }

        return SeasonalCampaign::query()
            ->enabled()
            ->get()
            ->filter(fn (SeasonalCampaign $c) => $c->isScheduledAt($now) && $c->matchesUser($userId))
            ->sort(function (SeasonalCampaign $a, SeasonalCampaign $b) {
                $aSpecific = ($a->audience_type ?? 'all_users') === 'specific_users' ? 1 : 0;
                $bSpecific = ($b->audience_type ?? 'all_users') === 'specific_users' ? 1 : 0;

                return [$b->priority, $bSpecific, $b->startsAt()?->getTimestamp() ?? 0, $b->id]
                    <=> [$a->priority, $aSpecific, $a->startsAt()?->getTimestamp() ?? 0, $a->id];
            })
            ->first();
    }

    /**
     * Public configuration payload (cached per audience). Always returns a complete array;
     * when nothing is active it is the disabled configuration.
     */
    public function getPublicConfig(?int $userId = null): array
    {
        $cacheKey = $this->cacheKeyFor($userId);
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $now = CarbonImmutable::now();
            $active = $this->resolveActive($now, $userId);
            $payload = $active
                ? (new SeasonalCampaignResource($active))->resolve()
                : SeasonalCampaignResource::disabled();

            Cache::put($cacheKey, $payload, $this->ttlFor($now));

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
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        Cache::forever(self::VERSION_KEY, $version + 1);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    protected function cacheKeyFor(?int $userId): string
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $suffix = $userId !== null ? "u:{$userId}" : 'guest';

        return self::CACHE_KEY . ":v{$version}:{$suffix}";
    }

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
