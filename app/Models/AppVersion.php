<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppVersion
 *
 * @property int         $id
 * @property string      $app_type         'android' | 'ios'
 * @property string      $version_name     e.g. "1.15.0"
 * @property int         $version_code     e.g. 115
 * @property bool        $force_update
 * @property string      $update_message
 * @property string|null $store_url
 * @property bool        $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class AppVersion extends Model
{
    protected $table = 'app_versions';

    protected $casts = [
        'version_code' => 'integer',
        'force_update' => 'boolean',
        'is_active'    => 'boolean',
    ];

    protected $fillable = [
        'app_type',
        'version_name',
        'version_code',
        'force_update',
        'update_message',
        'store_url',
        'is_active',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Only active records.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by platform (android | ios).
     */
    public function scopeForPlatform(Builder $query, string $platform): Builder
    {
        return $query->where('app_type', strtolower($platform));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns the latest active AppVersion record for the given platform,
     * ordered by version_code descending so the highest version wins.
     */
    public static function latestForPlatform(string $platform): ?self
    {
        return static::active()
            ->forPlatform($platform)
            ->orderByDesc('version_code')
            ->first();
    }
}
