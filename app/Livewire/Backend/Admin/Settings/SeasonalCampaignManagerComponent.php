<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Models\SeasonalCampaign;
use App\Services\Seasonal\SeasonalCampaignService;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Admin UI for seasonal / festive campaigns (Christmas, New Year, custom...).
 * One page: master switch, campaign list, create/edit form and a live preview.
 */
class SeasonalCampaignManagerComponent extends Component
{
    use WithFileUploads;

    public bool $masterEnabled = true;
    public bool $showForm = false;
    public ?int $editingId = null;

    /** Form state – mirrors the seasonal_campaigns columns. */
    public array $form = [];

    public $bannerUpload = null;
    public ?string $existingBanner = null;
    public bool $removeBanner = false;

    public const FLAGS = [
        'Logo' => ['show_santa_hat' => 'Santa hat on logo'],
        'Snow' => ['show_snowfall' => 'Falling snow'],
        'Decorations' => [
            'show_decorations' => 'Decorations (stars, bells, candy canes...)',
            'show_christmas_lights' => 'Christmas lights',
            'show_ornaments' => 'Ornaments & balls',
            'show_sparkles' => 'Sparkles & twinkles',
        ],
        'Greeting & banner' => [
            'show_greeting' => 'Home greeting',
            'show_seasonal_banner' => 'Seasonal promo banner',
        ],
        'New Year' => [
            'show_gold_accents' => 'Golden accents & borders',
            'show_gold_particles' => 'Falling gold particles',
            'show_confetti' => 'Golden confetti',
            'show_fireworks' => 'Fireworks',
            'show_countdown' => 'Countdown',
        ],
    ];

    public function mount(SeasonalCampaignService $service): void
    {
        $this->masterEnabled = $service->isMasterEnabled();
        $this->form = $this->defaults();
    }

    public function render()
    {
        $service = app(SeasonalCampaignService::class);
        $active = $service->resolveActive();

        return view('livewire.pages.backend.admin.settings.seasonal-campaign-manager-component', [
            'campaigns' => SeasonalCampaign::query()->orderByDesc('priority')->orderByDesc('start_date')->get(),
            'activeId' => $active?->id,
            'timezones' => DateTimeZone::listIdentifiers(),
            'flags' => self::FLAGS,
        ]);
    }

    // ── Master switch ───────────────────────────────────────────────────────

    public function updatedMasterEnabled(bool $value): void
    {
        app(SeasonalCampaignService::class)->setMasterEnabled($value);
        $this->alert('success', $value
            ? 'Seasonal experience is ON. Scheduled campaigns will show in the app.'
            : 'Seasonal experience is OFF. The app returns to its normal appearance.');
    }

    // ── CRUD ────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    /** Pre-fill sensible presets when the theme changes on a new campaign. */
    public function updatedFormTheme(string $theme): void
    {
        if ($this->editingId) {
            return;
        }

        $presets = [
            'christmas' => [
                'show_santa_hat' => true, 'show_snowfall' => true, 'show_decorations' => true,
                'show_christmas_lights' => true, 'show_sparkles' => true, 'show_ornaments' => true,
                'show_greeting' => true, 'greeting_message' => 'Merry Christmas!',
                'primary_festive_color' => '#C62828', 'secondary_festive_color' => '#2E7D32',
            ],
            'new_year' => [
                'show_santa_hat' => false, 'show_snowfall' => false, 'show_gold_accents' => true,
                'show_gold_particles' => true, 'show_confetti' => true, 'show_fireworks' => true,
                'show_sparkles' => true, 'show_greeting' => true, 'greeting_message' => 'Happy New Year!',
                'primary_festive_color' => '#D4AF37', 'secondary_festive_color' => '#1A1A2E',
            ],
        ];

        $this->form = array_merge($this->form, $presets[$theme] ?? []);
    }

    public function edit(int $id): void
    {
        $c = SeasonalCampaign::findOrFail($id);
        $this->resetForm();
        $this->editingId = $c->id;
        $this->existingBanner = $c->banner_image;

        $data = $c->only(array_keys($this->defaults()));
        $data['start_date'] = $c->getRawOriginal('start_date') ? substr($c->getRawOriginal('start_date'), 0, 10) : '';
        $data['end_date'] = $c->getRawOriginal('end_date') ? substr($c->getRawOriginal('end_date'), 0, 10) : '';
        $data['countdown_target_date'] = $c->getRawOriginal('countdown_target_date')
            ? str_replace(' ', 'T', substr($c->getRawOriginal('countdown_target_date'), 0, 16)) : '';
        $this->form = array_merge($this->defaults(), array_filter($data, fn ($v) => $v !== null));
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(): void
    {
        $this->validate($this->rules(), [], ['form.campaign_name' => 'campaign name']);

        $data = $this->form;
        foreach (['start_date', 'end_date', 'countdown_target_date'] as $k) {
            $data[$k] = $data[$k] === '' ? null : $data[$k];
        }
        foreach (['greeting_background_color', 'greeting_text_color', 'primary_festive_color', 'secondary_festive_color'] as $k) {
            $data[$k] = $data[$k] === '' ? null : $data[$k];
        }

        $campaign = $this->editingId ? SeasonalCampaign::findOrFail($this->editingId) : new SeasonalCampaign();

        if ($this->bannerUpload) {
            if ($campaign->banner_image) {
                Storage::disk('public')->delete($campaign->banner_image);
            }
            $data['banner_image'] = $this->bannerUpload->store('seasonal', 'public');
        } elseif ($this->removeBanner && $campaign->banner_image) {
            Storage::disk('public')->delete($campaign->banner_image);
            $data['banner_image'] = null;
        }

        $campaign->fill($data)->save(); // model events flush the API cache

        $this->alert('success', 'Seasonal campaign saved. The app picks it up on its next refresh.');
        $this->cancel();
    }

    public function toggle(int $id): void
    {
        $c = SeasonalCampaign::findOrFail($id);
        $c->update(['enabled' => ! $c->enabled]);
        $this->alert('success', $c->enabled ? 'Campaign activated.' : 'Campaign deactivated.');
    }

    public function destroy(int $id): void
    {
        $c = SeasonalCampaign::findOrFail($id);
        if ($c->banner_image) {
            Storage::disk('public')->delete($c->banner_image);
        }
        $c->delete();
        $this->alert('success', 'Campaign deleted.');
    }

    // ── Validation ──────────────────────────────────────────────────────────

    protected function rules(): array
    {
        $intensity = ['required', Rule::in(SeasonalCampaign::INTENSITIES)];
        $color = ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'];

        return [
            'form.theme' => ['required', Rule::in(SeasonalCampaign::THEMES)],
            'form.campaign_name' => ['required', 'string', 'max:120'],
            'form.campaign_description' => ['nullable', 'string', 'max:500'],
            'form.start_date' => ['nullable', 'date'],
            'form.end_date' => ['nullable', 'date', 'after_or_equal:form.start_date'],
            'form.timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],
            'form.priority' => ['required', 'integer', 'between:-1000,1000'],
            'form.logo_decoration' => ['nullable', 'string', 'max:40'],
            'form.snowfall_intensity' => $intensity,
            'form.snowfall_speed' => ['required', Rule::in(SeasonalCampaign::SPEEDS)],
            'form.snowfall_opacity' => ['required', 'numeric', 'between:0,1'],
            'form.decoration_intensity' => $intensity,
            'form.lights_intensity' => $intensity,
            'form.fireworks_intensity' => $intensity,
            'form.greeting_message' => ['nullable', 'string', 'max:160'],
            'form.greeting_subtitle' => ['nullable', 'string', 'max:240'],
            'form.greeting_alignment' => ['required', Rule::in(SeasonalCampaign::ALIGNMENTS)],
            'form.greeting_text_style' => ['required', Rule::in(SeasonalCampaign::TEXT_STYLES)],
            'form.greeting_background_color' => $color,
            'form.greeting_text_color' => $color,
            'form.banner_title' => ['nullable', 'string', 'max:120'],
            'form.banner_subtitle' => ['nullable', 'string', 'max:240'],
            'form.banner_link' => ['nullable', 'string', 'max:255', 'regex:#^(https?://\S+|/[A-Za-z0-9/_\-?=&.%]*)$#'],
            'form.celebration_message' => ['nullable', 'string', 'max:240'],
            'form.countdown_message' => ['nullable', 'string', 'max:160'],
            'form.countdown_target_date' => ['nullable', 'date', 'required_if:form.show_countdown,true'],
            'form.primary_festive_color' => $color,
            'form.secondary_festive_color' => $color,
            'form.background_effect_opacity' => ['required', 'numeric', 'between:0,1'],
            'bannerUpload' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    protected function defaults(): array
    {
        return [
            'theme' => 'christmas', 'campaign_name' => '', 'campaign_description' => '',
            'enabled' => true, 'start_date' => '', 'end_date' => '',
            'timezone' => config('app.timezone', 'Africa/Lagos'), 'priority' => 0,
            'show_santa_hat' => false, 'logo_decoration' => '',
            'show_snowfall' => false, 'snowfall_intensity' => 'medium', 'snowfall_speed' => 'normal', 'snowfall_opacity' => 0.5,
            'show_decorations' => false, 'show_christmas_lights' => false, 'show_sparkles' => false,
            'show_ornaments' => false, 'decoration_intensity' => 'medium', 'lights_intensity' => 'medium',
            'show_greeting' => false, 'greeting_message' => '', 'greeting_subtitle' => '',
            'greeting_alignment' => 'center', 'greeting_text_style' => 'normal',
            'greeting_background_color' => '', 'greeting_text_color' => '',
            'show_seasonal_banner' => false, 'banner_title' => '', 'banner_subtitle' => '', 'banner_link' => '',
            'show_gold_accents' => false, 'show_fireworks' => false, 'show_confetti' => false,
            'show_gold_particles' => false, 'show_countdown' => false, 'countdown_target_date' => '',
            'countdown_message' => '', 'fireworks_intensity' => 'low', 'celebration_message' => '',
            'primary_festive_color' => '', 'secondary_festive_color' => '', 'background_effect_opacity' => 1.0,
        ];
    }

    protected function resetForm(): void
    {
        $this->form = $this->defaults();
        $this->editingId = null;
        $this->bannerUpload = null;
        $this->existingBanner = null;
        $this->removeBanner = false;
        $this->resetValidation();
    }
}

