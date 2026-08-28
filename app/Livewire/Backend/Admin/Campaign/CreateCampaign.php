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
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * CreateCampaign
 *
 * Livewire component for the campaign creation form.
 * Handles all fields including image upload, visual condition builder, and visual CTA actions.
 */
class CreateCampaign extends Component
{
    use WithFileUploads;

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

    // ── Action visual fields ────────────────────────────────────────────────────
    public string  $action_type        = 'none';
    public ?string $action_product_id  = '';
    public ?string $action_category_id = '';
    public ?string $action_url         = '';
    public ?string $action_coupon_code = '';
    public ?string $action_order_id    = '';

    // ── UI Select Options ───────────────────────────────────────────────────────
    public array  $customerGroups   = [];
    public array  $categories       = [];
    public array  $coupons          = [];
    public array  $products         = [];
    public array  $triggerEvents    = [];
    public array  $actionTypes      = [];

    public function mount(): void
    {
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
            'image_upload'     => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:5120'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $imagePath = null;
        if ($this->image_upload && method_exists($this->image_upload, 'store')) {
            $imagePath = $this->image_upload->store('campaigns', 'public');
        }

        $conditions = $this->buildConditionsData();
        $actionData = $this->buildActionData();

        Campaign::create([
            'name'                     => $this->name,
            'slug'                     => Str::slug($this->name . '-' . Str::random(6)),
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
            'max_impressions'          => $this->max_impressions ? (int) $this->max_impressions : null,
            'max_impressions_per_user' => $this->max_impressions_per_user ? (int) $this->max_impressions_per_user : null,
            'cooldown_minutes'         => $this->cooldown_minutes ? (int) $this->cooldown_minutes : null,
            'random_probability'       => $this->random_probability ? (int) $this->random_probability : null,
            'image'                    => $imagePath,
            'title'                    => $this->title,
            'message'                  => $this->message,
            'cta_text'                 => $this->cta_text,
            'push_title'               => $this->push_title,
            'push_body'                => $this->push_body,
            'action_type'              => $this->action_type,
            'action_data'              => $actionData,
            'created_by'               => auth()->id(),
        ]);

        $this->alert('success', 'Campaign created successfully!');
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
        return view('livewire.backend.admin.campaign.create-campaign');
    }
}

