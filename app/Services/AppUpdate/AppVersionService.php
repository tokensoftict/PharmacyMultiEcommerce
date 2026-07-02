<?php

namespace App\Services\AppUpdate;

use App\Models\AppVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * AppVersionService
 * ─────────────────────────────────────────────────────────────────────────────
 * Resolves whether a mobile client needs to update its app.
 *
 * Version data is fetched from the `app_versions` table (with Redis caching)
 * and compared against the version the client sent in the request.
 *
 * Falls back to config/app_update.php if the database is unavailable.
 */
class AppVersionService
{
    /**
     * Resolve update information for the given platform and client version code.
     *
     * @param  string $platform    'android' or 'ios'
     * @param  string $clientVersion     e.g. "1.14"
     * @param  int    $clientVersionCode e.g. 114
     * @return array{
     *     has_update: bool,
     *     force_update: bool,
     *     latest_version: string,
     *     latest_version_code: int,
     *     current_version: string,
     *     current_version_code: int,
     *     store_url: string,
     *     update_message: string
     * }
     */
    public function resolve(string $platform, string $clientVersion, int $clientVersionCode): array
    {
        $platform = strtolower($platform);

        try {
            $latest = $this->getLatestVersion($platform);
        } catch (Throwable $e) {
            Log::warning("[AppVersionService] DB lookup failed, using config fallback. Error: {$e->getMessage()}");
            $latest = $this->getFallback($platform);
        }

        $hasUpdate = $clientVersionCode < $latest['latest_version_code'];

        return [
            'has_update'          => $hasUpdate,
            'force_update'        => $hasUpdate && $latest['force_update'],
            'latest_version'      => $latest['latest_version'],
            'latest_version_code' => $latest['latest_version_code'],
            'current_version'     => $clientVersion,
            'current_version_code'=> $clientVersionCode,
            'store_url'           => $latest['store_url'] ?? '',
            'update_message'      => $latest['update_message'],
        ];
    }

    /**
     * Fetch (and cache) the latest version info from the database.
     *
     * @throws \RuntimeException if no active record is found
     */
    private function getLatestVersion(string $platform): array
    {
        $cacheKey = config('app_update.cache_key', 'app_version') . ":{$platform}";
        $cacheTtl = (int) config('app_update.cache_ttl', 300);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($platform) {
            $record = AppVersion::latestForPlatform($platform);

            if (! $record) {
                // No DB record – throw so we fall back to config
                throw new \RuntimeException("No active AppVersion found for platform: {$platform}");
            }

            return [
                'latest_version'      => $record->version_name,
                'latest_version_code' => $record->version_code,
                'force_update'        => $record->force_update,
                'store_url'           => $record->store_url ?? '',
                'update_message'      => $record->update_message,
            ];
        });
    }

    /**
     * Config-file fallback (used when DB is unavailable).
     */
    private function getFallback(string $platform): array
    {
        $cfg = config("app_update.{$platform}", config('app_update.android'));

        return [
            'latest_version'      => $cfg['latest_version'],
            'latest_version_code' => (int) $cfg['latest_version_code'],
            'force_update'        => (bool) $cfg['force_update'],
            'store_url'           => $cfg['store_url'] ?? '',
            'update_message'      => $cfg['update_message'],
        ];
    }

    /**
     * Bust the version cache for a given platform.
     * Call this from the admin panel after updating a version record.
     */
    public function clearCache(string $platform): void
    {
        $cacheKey = config('app_update.cache_key', 'app_version') . ":{$platform}";
        Cache::forget($cacheKey);
    }

    /**
     * Bust the version cache for all platforms.
     */
    public function clearAllCache(): void
    {
        $this->clearCache('android');
        $this->clearCache('ios');
    }
}
