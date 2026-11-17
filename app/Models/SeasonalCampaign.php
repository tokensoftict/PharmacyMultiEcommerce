<?php

namespace App\Models;

use App\Services\Seasonal\SeasonalCampaignService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A scheduled seasonal / festive experience (Christmas, New Year, Easter, ...).
 *
 * Dates are wall-clock values interpreted in the campaign's own `timezone`.
 * `end_date` is inclusive (the campaign runs through 23:59:59 of that day).
 */
class SeasonalCampaign extends Model
{
    public const THEMES = ['christmas', 'new_year', 'custom'];
    public const INTENSITIES = ['low', 'medium', 'high'];
    public const SPEEDS = ['slow', 'normal', 'fast'];
    public const ALIGNMENTS = ['left', 'center', 'right'];
    public const TEXT_STYLES = ['normal', 'bold', 'italic', 'script'];

    protected $table = 'seasonal_campaigns';

    protected $guarded = ['id'];

    protected $casts = [
        'enabled' => 'boolean',
        'priority' => 'integer',
        'show_santa_hat' => 'boolean',
        'show_snowfall' => 'boolean',
        'snowfall_opacity' => 'float',
        'show_decorations' => 'boolean',
        'show_christmas_lights' => 'boolean',
        'show_sparkles' => 'boolean',
        'show_ornaments' => 'boolean',
        'show_greeting' => 'boolean',
        'show_seasonal_banner' => 'boolean',
        'show_gold_accents' => 'boolean',
        'show_fireworks' => 'boolean',
        'show_confetti' => 'boolean',
        'show_gold_particles' => 'boolean',
        'show_countdown' => 'boolean',
        'background_effect_opacity' => 'float',
        'extra_effects' => 'array',
    ];

    protected static function booted(): void
    {
        $flush = fn () => app(SeasonalCampaignService::class)->flushCache();

        static::saved($flush);
        static::deleted($flush);
    }

    /** Start of the campaign as an instant (null = no lower bound). */
    public function startsAt(): ?CarbonImmutable
    {
        $raw = $this->getRawOriginal('start_date');

        return $raw ? CarbonImmutable::parse(substr((string) $raw, 0, 10), $this->tz())->startOfDay() : null;
    }

    /** End of the campaign as an instant, inclusive of the whole end day (null = no upper bound). */
    public function endsAt(): ?CarbonImmutable
    {
        $raw = $this->getRawOriginal('end_date');

        return $raw ? CarbonImmutable::parse(substr((string) $raw, 0, 10), $this->tz())->endOfDay() : null;
    }

    public function countdownTarget(): ?CarbonImmutable
    {
        $raw = $this->getRawOriginal('countdown_target_date');

        return $raw ? CarbonImmutable::parse((string) $raw, $this->tz()) : null;
    }

    public function tz(): string
    {
        $tz = $this->timezone;

        return $tz && in_array($tz, \DateTimeZone::listIdentifiers(), true) ? $tz : config('app.timezone', 'UTC');
    }

    /** Is the campaign inside its schedule window at the given instant? */
    public function isScheduledAt(CarbonImmutable $now): bool
    {
        $start = $this->startsAt();
        $end = $this->endsAt();

        return (! $start || $now->greaterThanOrEqualTo($start))
            && (! $end || $now->lessThanOrEqualTo($end));
    }

    public function isActiveAt(CarbonImmutable $now): bool
    {
        return $this->enabled && $this->isScheduledAt($now);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}

