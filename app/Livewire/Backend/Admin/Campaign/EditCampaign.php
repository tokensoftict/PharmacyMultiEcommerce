<?php

namespace App\Livewire\Backend\Admin\Campaign;

use App\Enums\CampaignActionType;
use App\Enums\CampaignEvent;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Models\CustomerGroup;
use App\Models\Productcategory;
use App\Models\Stock;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * EditCampaign
 *
 * Livewire component for updating an existing campaign.
 */
class EditCampaign extends Component
{
    use WithFileUploads;

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

    // ── Trigger & conditions builder ────────────────────────────────────────────
    public string $trigger_event        = 'APP_OPEN';
    public string $condition_match_type = 'AND'; // AND | OR
    public array  $condition_rules      = [];    // [['field' => 'cart_total', 'operator' => '>=', 'value' => '']]

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

    // ── Action visual fields ────────────────────────────────────────────────────
    public string  $action_type        = 'none';
    public ?string $action_product_id  = '';
    public ?string $action_category_id = '';
    public ?string $action_url         = '';
    public ?string $action_coupon_code = '';
    public ?string $action_order_id    = '';

    // ── UI options ──────────────────────────────────────────────────────────────
    public array  $customerGroups   = [];
    public array  $categories       = [];
    public array  $coupons          = [];
    public array  $products         = [];
    public array  $triggerEvents    = [];
    public array  $actionTypes      = [];

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;

        $this->name                     = $campaign->name;
        $this->description              = $campaign->description ?? '';
        $this->status                   = $campaign->status;
        $this->priority                 = $campaign->priority;
        $this->store_type               = $campaign->store_type;
        $this->audience_type            = $campaign->audience_type;
        $this->audience_ids             = $campaign->audience_ids ?? [];
        $this->starts_at                = $campaign->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at                  = $campaign->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->trigger_event            = $campaign->trigger_event ?? 'APP_OPEN';
        $this->delivery_channel         = $campaign->delivery_channel;
        $this->display_type             = $campaign->display_type;
        $this->frequency_rule           = $campaign->frequency_rule;
        $this->max_impressions          = (string) ($campaign->max_impressions ?? '');
        $this->max_impressions_per_user = (string) ($campaign->max_impressions_per_user ?? '');
        $this->cooldown_minutes         = (string) ($campaign->cooldown_minutes ?? '');
        $this->random_probability       = (string) ($campaign->random_probability ?? '');
        $this->title                    = $campaign->title ?? '';
        $this->message                  = $campaign->message ?? '';
        $this->cta_text                 = $campaign->cta_text ?? '';
        $this->push_title               = $campaign->push_title ?? '';
        $this->push_body                = $campaign->push_body ?? '';
        $this->existing_image           = $campaign->image;
        $this->action_type              = $campaign->action_type;

        // Populate action UI fields from $campaign->action_data
        $actionData = $campaign->action_data ?? [];
        $this->action_product_id  = (string) ($actionData['product_id'] ?? $actionData['id'] ?? '');
        $this->action_category_id = (string) ($actionData['category_id'] ?? '');
        $this->action_url         = (string) ($actionData['url'] ?? $actionData['link'] ?? $actionData['deep_link'] ?? '');
        $this->action_coupon_code = (string) ($actionData['coupon_code'] ?? $actionData['code'] ?? '');
        $this->action_order_id    = (string) ($actionData['order_id'] ?? '');

        // Populate condition rules from $campaign->conditions
        $conditions = $campaign->conditions ?? [];
        if (!empty($conditions['rules']) && is_array($conditions['rules'])) {
            $this->condition_match_type = $conditions['operator'] ?? 'AND';
            $this->condition_rules = array_map(fn($r) => [
                'field'    => $r['field'] ?? 'cart_total',
                'operator' => $r['operator'] ?? '>=',
                'value'    => (string) ($r['value'] ?? ''),
            ], $conditions['rules']);
        } else {
            $this->condition_rules = [];
        }

        $this->customerGroups = CustomerGroup::orderBy('name')->get(['id', 'name'])->toArray();
        $this->categories     = Productcategory::orderBy('name')->get(['id', 'name'])->toArray();
        $this->coupons        = Coupon::orderBy('name')->get(['id', 'name', 'code'])->toArray();
        $this->products       = Stock::select(['id', 'name'])->orderBy('name')->take(250)->get()->toArray();
        $this->triggerEvents  = CampaignEvent::all();
        $this->actionTypes    = CampaignActionType::all();
    }

    public function addConditionRule(): void
    {
        $this->condition_rules[] = [
            'field'    => 'cart_total',
            'operator' => '>=',
            'value'    => '',
        ];
    }

    public function removeConditionRule(int $index): void
    {
        unset($this->condition_rules[$index]);
        $this->condition_rules = array_values($this->condition_rules);
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

        $conditions = $this->buildConditionsData();
        $actionData = $this->buildActionData();

        $this->campaign->update([
            'name'                     => $this->name,
            'description'              => $this->description,
            'status'                   => $this->status,
            'priority'                 => $this->priority,
            'store_type'               => $this->store_type,
            'audience_type'            => $this->audience_type,
            'audience_ids'             => empty($this->audience_ids) ? null : $this->audience_ids,
            'starts_at'                => $this->starts_at ?: null,
            'ends_at'                  => $this->ends_at ?: null,
            'trigger_event'            => $this->trigger_event,
            'conditions'               => $conditions,
            'delivery_channel'         => $this->delivery_channel,
            'display_type'             => $this->display_type,
            'frequency_rule'           => $this->frequency_rule,
            'max_impressions'          => $this->max_impressions !== '' ? (int) $this->max_impressions : null,
            'max_impressions_per_user' => $this->max_impressions_per_user !== '' ? (int) $this->max_impressions_per_user : null,
            'cooldown_minutes'         => $this->cooldown_minutes !== '' ? (int) $this->cooldown_minutes : null,
            'random_probability'       => $this->random_probability !== '' ? (int) $this->random_probability : null,
            'image'                    => $imagePath,
            'title'                    => $this->title,
            'message'                  => $this->message,
            'cta_text'                 => $this->cta_text,
            'push_title'               => $this->push_title,
            'push_body'                => $this->push_body,
            'action_type'              => $this->action_type,
            'action_data'              => $actionData,
        ]);

        $this->alert('success', 'Campaign updated successfully!');
        $this->redirect(route('backend.admin.campaign.list'));
    }

    private function buildConditionsData(): array
    {
        $validRules = [];
        foreach ($this->condition_rules as $rule) {
            if (isset($rule['field'], $rule['operator'], $rule['value']) && $rule['value'] !== '') {
                $val = is_numeric($rule['value']) ? (float) $rule['value'] : $rule['value'];
                $validRules[] = [
                    'field'    => $rule['field'],
                    'operator' => $rule['operator'],
                    'value'    => $val,
                ];
            }
        }

        if (empty($validRules)) {
            return [];
        }

        return [
            'operator' => $this->condition_match_type,
            'rules'    => $validRules,
        ];
    }

    private function buildActionData(): array
    {
        return match ($this->action_type) {
            'open_product' => array_filter([
                'product_id' => $this->action_product_id ? (int) $this->action_product_id : null,
            ]),
            'open_category' => array_filter([
                'category_id'   => $this->action_category_id ? (int) $this->action_category_id : null,
                'category_name' => $this->resolveCategoryName($this->action_category_id),
            ]),
            'open_url', 'open_deep_link' => array_filter([
                'url' => $this->action_url ?: null,
            ]),
            'apply_coupon' => array_filter([
                'coupon_code' => $this->action_coupon_code ?: null,
            ]),
            'open_order' => array_filter([
                'order_id' => $this->action_order_id ? (int) $this->action_order_id : null,
            ]),
            default => [],
        };
    }

    private function resolveCategoryName(?string $categoryId): ?string
    {
        if (!$categoryId) return null;
        foreach ($this->categories as $cat) {
            if ($cat['id'] == $categoryId) {
                return $cat['name'];
            }
        }
        return null;
    }

    public function render()
    {
        return view('livewire.backend.admin.campaign.edit-campaign');
    }
}

