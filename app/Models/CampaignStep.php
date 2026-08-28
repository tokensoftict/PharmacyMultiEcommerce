<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CampaignStep
 *
 * @property int $id
 * @property int $campaign_id
 * @property int $step_number
 * @property int $delay_minutes
 * @property string $delivery_channel
 * @property string|null $display_type
 * @property string|null $title
 * @property string|null $message
 * @property string|null $image
 * @property string|null $cta_text
 * @property array|null $conditions
 * @property string $action_type
 * @property array|null $action_data
 * @property bool $is_active
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Campaign $campaign
 */
class CampaignStep extends Model
{
    protected $table = 'campaign_steps';

    protected $casts = [
        'delay_minutes' => 'int',
        'conditions'    => 'array',
        'action_data'   => 'array',
        'is_active'     => 'bool',
        'priority'      => 'int',
        'step_number'   => 'int',
    ];

    protected $fillable = [
        'campaign_id', 'step_number', 'delay_minutes',
        'delivery_channel', 'display_type',
        'title', 'message', 'image', 'cta_text',
        'conditions', 'action_type', 'action_data',
        'is_active', 'priority',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Resolve effective content — falls back to parent campaign values.
     */
    public function resolveTitle(): ?string
    {
        return $this->title ?? $this->campaign->title;
    }

    public function resolveMessage(): ?string
    {
        return $this->message ?? $this->campaign->message;
    }

    public function resolveImage(): ?string
    {
        return $this->image ?? $this->campaign->image;
    }

    public function resolveActionType(): string
    {
        return $this->action_type !== 'none' ? $this->action_type : $this->campaign->action_type;
    }

    public function resolveActionData(): ?array
    {
        return $this->action_data ?? $this->campaign->action_data;
    }

    public function resolvePushTitle(): ?string
    {
        return $this->title ?? $this->campaign->push_title ?? $this->campaign->title;
    }

    public function resolvePushBody(): ?string
    {
        return $this->message ?? $this->campaign->push_body ?? $this->campaign->message;
    }
}
