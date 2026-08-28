<?php

namespace App\Models;

use App\Enums\CampaignActivityType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CampaignActivity
 *
 * @property int $id
 * @property int $campaign_id
 * @property int|null $campaign_step_id
 * @property int $user_id
 * @property string $event_type
 * @property string|null $channel
 * @property string|null $device_token
 * @property string|null $platform
 * @property string|null $session_id
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $opened_at
 * @property Carbon|null $clicked_at
 * @property Carbon|null $converted_at
 * @property string|null $attributed_to
 * @property array|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CampaignActivity extends Model
{
    protected $table = 'campaign_activities';

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
        'opened_at'    => 'datetime',
        'clicked_at'   => 'datetime',
        'converted_at' => 'datetime',
        'metadata'     => 'array',
    ];

    protected $fillable = [
        'campaign_id', 'campaign_step_id', 'user_id',
        'event_type', 'channel', 'device_token', 'platform', 'session_id',
        'scheduled_at', 'sent_at', 'opened_at', 'clicked_at', 'converted_at',
        'attributed_to', 'metadata',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function step()
    {
        return $this->belongsTo(CampaignStep::class, 'campaign_step_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Static factory helpers ─────────────────────────────────────────────────

    public static function logImpression(Campaign $campaign, int $userId, string $channel, array $meta = []): self
    {
        $activity = self::create([
            'campaign_id' => $campaign->id,
            'user_id'     => $userId,
            'event_type'  => CampaignActivityType::IMPRESSION,
            'channel'     => $channel,
            'metadata'    => $meta,
        ]);

        $campaign->increment('total_impressions');

        return $activity;
    }

    public static function logClick(Campaign $campaign, int $userId, string $channel, array $meta = []): self
    {
        $activity = self::create([
            'campaign_id' => $campaign->id,
            'user_id'     => $userId,
            'event_type'  => CampaignActivityType::CLICKED,
            'channel'     => $channel,
            'clicked_at'  => now(),
            'metadata'    => $meta,
        ]);

        $campaign->increment('total_clicks');

        return $activity;
    }

    public static function logDismissal(Campaign $campaign, int $userId, string $channel, array $meta = []): self
    {
        $activity = self::create([
            'campaign_id' => $campaign->id,
            'user_id'     => $userId,
            'event_type'  => CampaignActivityType::DISMISSED,
            'channel'     => $channel,
            'metadata'    => $meta,
        ]);

        $campaign->increment('total_dismissals');

        return $activity;
    }

    public static function logConversion(Campaign $campaign, int $userId, string $attributedTo, array $meta = []): self
    {
        $activity = self::create([
            'campaign_id'   => $campaign->id,
            'user_id'       => $userId,
            'event_type'    => CampaignActivityType::CONVERTED,
            'attributed_to' => $attributedTo,
            'converted_at'  => now(),
            'metadata'      => $meta,
        ]);

        $campaign->increment('total_conversions');

        return $activity;
    }
}
