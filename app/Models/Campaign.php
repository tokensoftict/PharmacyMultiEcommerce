<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Campaign
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $status
 * @property int $priority
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property string $store_type
 * @property string $audience_type
 * @property array|null $audience_ids
 * @property string|null $trigger_event
 * @property array|null $conditions
 * @property string $delivery_channel
 * @property string $display_type
 * @property string $frequency_rule
 * @property int|null $max_impressions
 * @property int|null $max_impressions_per_user
 * @property int|null $max_clicks
 * @property int|null $cooldown_minutes
 * @property int|null $random_probability
 * @property string|null $image
 * @property string|null $title
 * @property string|null $message
 * @property string|null $cta_text
 * @property string|null $push_title
 * @property string|null $push_body
 * @property string $action_type
 * @property array|null $action_data
 * @property int $total_impressions
 * @property int $total_clicks
 * @property int $total_dismissals
 * @property int $total_conversions
 * @property int $total_push_sent
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property Collection|CampaignStep[] $steps
 * @property Collection|CampaignActivity[] $activities
 */
class Campaign extends Model
{
    use SoftDeletes;

    protected $table = 'campaigns';

    protected $casts = [
        'starts_at'              => 'datetime',
        'ends_at'                => 'datetime',
        'audience_ids'           => 'array',
        'conditions'             => 'array',
        'action_data'            => 'array',
        'priority'               => 'int',
        'max_impressions'        => 'int',
        'max_impressions_per_user' => 'int',
        'max_clicks'             => 'int',
        'cooldown_minutes'       => 'int',
        'random_probability'     => 'int',
        'total_impressions'      => 'int',
        'total_clicks'           => 'int',
        'total_dismissals'       => 'int',
        'total_conversions'      => 'int',
        'total_push_sent'        => 'int',
    ];

    protected $fillable = [
        'name', 'slug', 'description', 'status', 'priority',
        'starts_at', 'ends_at', 'store_type', 'audience_type', 'audience_ids',
        'trigger_event', 'conditions', 'delivery_channel', 'display_type',
        'frequency_rule', 'max_impressions', 'max_impressions_per_user',
        'max_clicks', 'cooldown_minutes', 'random_probability',
        'image', 'title', 'message', 'cta_text',
        'push_title', 'push_body',
        'action_type', 'action_data',
        'total_impressions', 'total_clicks', 'total_dismissals',
        'total_conversions', 'total_push_sent',
        'created_by',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function steps()
    {
        return $this->hasMany(CampaignStep::class)->orderBy('step_number');
    }

    public function activities()
    {
        return $this->hasMany(CampaignActivity::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', CampaignStatus::ACTIVE)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    public function scopeForStore($query, string $storeType)
    {
        return $query->where(function ($q) use ($storeType) {
            $q->where('store_type', 'both')->orWhere('store_type', $storeType);
        });
    }

    public function scopeForTrigger($query, string $triggerEvent)
    {
        return $query->where('trigger_event', $triggerEvent);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        if ($this->status !== CampaignStatus::ACTIVE) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->ends_at && $this->ends_at->isPast()) return false;
        return true;
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
