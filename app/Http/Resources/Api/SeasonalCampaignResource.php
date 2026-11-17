<?php

namespace App\Http\Resources\Api;

use App\Models\SeasonalCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Public, sanitised representation of the live seasonal campaign.
 *
 * Every value is validated/clamped here so the mobile app can trust the payload.
 *
 * @mixin SeasonalCampaign
 */
class SeasonalCampaignResource extends JsonResource
{
    public static $wrap = null;

    /** Configuration returned when nothing is active – the app renders its normal appearance. */
    public static function disabled(): array
    {
        return ['enabled' => false, 'theme' => null];
    }

    public function toArray(Request $request): array
    {
        /** @var SeasonalCampaign $c */
        $c = $this->resource;
        $tz = $c->tz();
        $on = fn (string $flag): bool => (bool) $c->{$flag};

        return [
            'enabled' => true,
            'id' => $c->id,
            'theme' => self::oneOf($c->theme, SeasonalCampaign::THEMES, 'custom'),
            'campaign_name' => self::text($c->campaign_name, 120),
            'campaign_description' => self::text($c->campaign_description, 500),
            'audience_type' => self::oneOf($c->audience_type, SeasonalCampaign::AUDIENCE_TYPES, 'all_users'),
            'priority' => (int) $c->priority,
            'timezone' => $tz,
            'start_date' => $c->startsAt()?->format('Y-m-d'),
            'end_date' => $c->endsAt()?->format('Y-m-d'),
            'starts_at' => $c->startsAt()?->toIso8601String(),
            'ends_at' => $c->endsAt()?->toIso8601String(),

            // Logo
            'show_santa_hat' => $on('show_santa_hat'),
            'logo_decoration' => self::text($c->logo_decoration, 40),

            // Snow
            'show_snowfall' => $on('show_snowfall'),
            'snowfall_intensity' => self::oneOf($c->snowfall_intensity, SeasonalCampaign::INTENSITIES, 'medium'),
            'snowfall_speed' => self::oneOf($c->snowfall_speed, SeasonalCampaign::SPEEDS, 'normal'),
            'snowfall_opacity' => self::unit($c->snowfall_opacity, 0.5),

            // Decorations
            'show_decorations' => $on('show_decorations'),
            'show_christmas_lights' => $on('show_christmas_lights'),
            'show_sparkles' => $on('show_sparkles'),
            'show_ornaments' => $on('show_ornaments'),
            'decoration_intensity' => self::oneOf($c->decoration_intensity, SeasonalCampaign::INTENSITIES, 'medium'),
            'lights_intensity' => self::oneOf($c->lights_intensity, SeasonalCampaign::INTENSITIES, 'medium'),

            // Greeting
            'show_greeting' => $on('show_greeting'),
            'greeting_message' => self::text($c->greeting_message, 160),
            'greeting_subtitle' => self::text($c->greeting_subtitle, 240),
            'greeting_alignment' => self::oneOf($c->greeting_alignment, SeasonalCampaign::ALIGNMENTS, 'center'),
            'greeting_text_style' => self::oneOf($c->greeting_text_style, SeasonalCampaign::TEXT_STYLES, 'normal'),
            'greeting_background_color' => self::color($c->greeting_background_color),
            'greeting_text_color' => self::color($c->greeting_text_color),

            // Promotional banner
            'show_seasonal_banner' => $on('show_seasonal_banner'),
            'banner_image' => $c->banner_image ? Storage::disk('public')->url($c->banner_image) : null,
            'banner_title' => self::text($c->banner_title, 120),
            'banner_subtitle' => self::text($c->banner_subtitle, 240),
            'banner_link' => self::link($c->banner_link),

            // New Year
            'show_gold_accents' => $on('show_gold_accents'),
            'show_fireworks' => $on('show_fireworks'),
            'show_confetti' => $on('show_confetti'),
            'show_gold_particles' => $on('show_gold_particles'),
            'fireworks_intensity' => self::oneOf($c->fireworks_intensity, SeasonalCampaign::INTENSITIES, 'low'),
            'celebration_message' => self::text($c->celebration_message, 240),
            'show_countdown' => $on('show_countdown') && $c->countdownTarget() !== null,
            'countdown_target_date' => $c->countdownTarget()?->toIso8601String(),
            'countdown_message' => self::text($c->countdown_message, 160),

            // Visual
            'primary_festive_color' => self::color($c->primary_festive_color),
            'secondary_festive_color' => self::color($c->secondary_festive_color),
            'background_effect_opacity' => self::unit($c->background_effect_opacity, 1.0),

            // Future effects: only boolean/scalar flags are passed through.
            'extra_effects' => self::extras($c->extra_effects),
        ];
    }

    public function with(Request $request): array
    {
        return [];
    }

    // ── Sanitisers ──────────────────────────────────────────────────────────

    public static function text(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = trim(preg_replace('/\s+/u', ' ', strip_tags($value)));

        return $clean === '' ? null : mb_substr($clean, 0, $max);
    }

    public static function oneOf(?string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    public static function unit(mixed $value, float $default): float
    {
        return is_numeric($value) ? round(min(1.0, max(0.0, (float) $value)), 2) : $default;
    }

    public static function color(?string $value): ?string
    {
        return $value && preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $value) ? $value : null;
    }

    /** Only http(s) URLs or in-app paths ("/products/...") – never javascript: etc. */
    public static function link(?string $value): ?string
    {
        $value = $value ? trim($value) : null;
        if (! $value) {
            return null;
        }

        if (preg_match('#^https?://#i', $value) && filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return preg_match('#^/[A-Za-z0-9/_\-?=&.%]*$#', $value) ? $value : null;
    }

    private static function extras(?array $extras): array
    {
        $out = [];
        foreach ((array) $extras as $key => $value) {
            if (is_string($key) && preg_match('/^[a-z0-9_]{1,40}$/', $key) && (is_bool($value) || is_numeric($value))) {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}

