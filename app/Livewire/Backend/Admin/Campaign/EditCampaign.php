<?php

namespace App\Livewire\Backend\Admin\Campaign;

use App\Enums\CampaignActionType;
use App\Enums\CampaignEvent;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CustomerGroup;
use Jantinnerezo\LivewireAlert\Concerns\SweetAlert2 as LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * EditCampaign
 *
 * Livewire component for updating an existing campaign.
 */
class EditCampaign extends Component
{
    use LivewireAlert, WithFileUploads;

    public Campaign $campaign;

    // ── Basic settings ──────────────────────────────────────────────────────────
    public string $name             = '';
    public string $description      = '';
    public string $status           = 'draft';
    public int    $priority         = 0;
    public string $store_type       = 'both';
    public string $audience_type    = 'all_users';
    public array  $audience_ids     = [];

    // ── Schedule ────────────────────────────────────────────────────────────────
    public string $starts_at        = '';
    public string $ends_at          = '';

    // ── Trigger & conditions ────────────────────────────────────────────────────
    public string $trigger_event    = 'APP_OPEN';
    public string $conditions_json  = '{}';

    // ── Delivery ────────────────────────────────────────────────────────────────
    public string $delivery_channel = 'both';
    public string $display_type     = 'modal';
    public string $frequency_rule   = 'once_ever';
    public string $max_impressions  = '';
    public string $max_impressions_per_user = '';
    public string $cooldown_minutes = '';
    public string $random_probability = '';

    // ── Content ─────────────────────────────────────────────────────────────────
    public string $title            = '';
    public string $message          = '';
    public string $cta_text         = '';
    public string $push_title       = '';
    public string $push_body        = '';
    public        $image_upload     = null;
    public ?string $existing_image  = null;

    // ── Action ──────────────────────────────────────────────────────────────────
    public string $action_type      = 'none';
    public string $action_data_json = '{}';

    // ── UI options ──────────────────────────────────────────────────────────────
    public array  $customerGroups   = [];
    public array  $triggerEvents    = [];
    public array  $actionTypes      = [];

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->name                    = $campaign->name;
        $this->description             = $campaign->description ?? '';
        $this->status                  = $campaign->status;
        $this->priority                = $campaign->priority;
        $this->store_type              = $campaign->store_type;
        $this->audience_type           = $campaign->audience_type;
        $this->audience_ids            = $campaign->audience_ids ?? [];
        $this->starts_at               = $campaign->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at                 = $campaign->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->trigger_event           = $campaign->trigger_event ?? 'APP_OPEN';
        $this->conditions_json         = json_encode($campaign->conditions ?? [], JSON_PRETTY_PRINT);
        $this->delivery_channel        = $campaign->delivery_channel;
        $this->display_type            = $campaign->display_type;
        $this->frequency_rule          = $campaign->frequency_rule;
        $this->max_impressions         = (string) ($campaign->max_impressions ?? '');
        $this->max_impressions_per_user = (string) ($campaign->max_impressions_per_user ?? '');
        $this->cooldown_minutes        = (string) ($campaign->cooldown_minutes ?? '');
        $this->random_probability      = (string) ($campaign->random_probability ?? '');
        $this->title                   = $campaign->title ?? '';
        $this->message                 = $campaign->message ?? '';
        $this->cta_text                = $campaign->cta_text ?? '';
        $this->push_title              = $campaign->push_title ?? '';
        $this->push_body               = $campaign->push_body ?? '';
        $this->existing_image          = $campaign->image;
        $this->action_type             = $campaign->action_type;
        $this->action_data_json        = json_encode($campaign->action_data ?? [], JSON_PRETTY_PRINT);

        $this->customerGroups = CustomerGroup::orderBy('name')->get(['id', 'name'])->toArray();
        $this->triggerEvents  = CampaignEvent::all();
        $this->actionTypes    = CampaignActionType::all();
    }

    protected function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'min:3', 'max:255'],
            'status'           => ['required', 'in:' . implode(',', CampaignStatus::all())],
            'trigger_event'    => ['required', 'string'],
            'delivery_channel' => ['required', 'in:in_app,push,both'],
            'display_type'     => ['required', 'in:modal,fullscreen,bottom_sheet,banner'],
            'frequency_rule'   => ['required', 'string'],
            'action_type'      => ['required', 'string'],
            'title'            => ['nullable', 'string', 'max:255'],
            'message'          => ['nullable', 'string'],
            'image_upload'     => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $imagePath = $this->existing_image;
        if ($this->image_upload) {
            $imagePath = $this->image_upload->store('campaigns', 'public');
        }

        $conditions = [];
        if ($this->conditions_json && $this->conditions_json !== '{}') {
            $conditions = json_decode($this->conditions_json, true) ?? [];
        }

        $actionData = [];
        if ($this->action_data_json && $this->action_data_json !== '{}') {
            $actionData = json_decode($this->action_data_json, true) ?? [];
        }

        $this->campaign->update([
            'name'                    => $this->name,
            'description'             => $this->description,
            'status'                  => $this->status,
            'priority'                => $this->priority,
            'store_type'              => $this->store_type,
            'audience_type'           => $this->audience_type,
            'audience_ids'            => empty($this->audience_ids) ? null : $this->audience_ids,
            'starts_at'               => $this->starts_at ?: null,
            'ends_at'                 => $this->ends_at ?: null,
            'trigger_event'           => $this->trigger_event,
            'conditions'              => $conditions,
            'delivery_channel'        => $this->delivery_channel,
            'display_type'            => $this->display_type,
            'frequency_rule'          => $this->frequency_rule,
            'max_impressions'         => $this->max_impressions !== '' ? (int) $this->max_impressions : null,
            'max_impressions_per_user' => $this->max_impressions_per_user !== '' ? (int) $this->max_impressions_per_user : null,
            'cooldown_minutes'        => $this->cooldown_minutes !== '' ? (int) $this->cooldown_minutes : null,
            'random_probability'      => $this->random_probability !== '' ? (int) $this->random_probability : null,
            'image'                   => $imagePath,
            'title'                   => $this->title,
            'message'                 => $this->message,
            'cta_text'                => $this->cta_text,
            'push_title'              => $this->push_title,
            'push_body'               => $this->push_body,
            'action_type'             => $this->action_type,
            'action_data'             => $actionData,
        ]);

        $this->alert('success', 'Campaign updated successfully!');
        $this->redirect(route('backend.admin.campaign.list'));
    }

    public function render()
    {
        return view('livewire.backend.admin.campaign.edit-campaign')
            ->layout('layouts.app');
    }
}
