<?php

namespace Tests\Feature;

use App\Livewire\Backend\Admin\Settings\SeasonalCampaignManagerComponent;
use App\Models\SeasonalCampaign;
use App\Services\Seasonal\SeasonalCampaignService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class SeasonalCampaignAdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_11_17_090000_create_seasonal_campaigns_table.php',
        ]);
        config(['cache.default' => 'array']);
        Cache::flush();
        // Normally set by DetectApplicationEnvironment middleware.
        \App\Classes\ApplicationEnvironment::$storePrefix = config('app.SUPERMARKET_ADMIN_ROUTE_PREFIX');
    }

    public function test_admin_can_create_edit_toggle_and_delete_a_campaign(): void
    {
        Storage::fake('public');

        Livewire::test(SeasonalCampaignManagerComponent::class)
            ->call('create')
            ->set('form.campaign_name', 'Christmas 2026')
            ->set('form.start_date', '2026-12-01')
            ->set('form.end_date', '2026-12-25')
            ->set('form.show_snowfall', true)
            ->set('bannerUpload', UploadedFile::fake()->image('banner.png'))
            ->call('save')
            ->assertHasNoErrors();

        $campaign = SeasonalCampaign::firstOrFail();
        $this->assertSame('Christmas 2026', $campaign->campaign_name);
        $this->assertTrue($campaign->show_snowfall);
        Storage::disk('public')->assertExists($campaign->banner_image);

        Livewire::test(SeasonalCampaignManagerComponent::class)
            ->call('edit', $campaign->id)
            ->set('form.greeting_message', 'Merry Christmas!')
            ->call('save')
            ->assertHasNoErrors();
        $this->assertSame('Merry Christmas!', $campaign->fresh()->greeting_message);

        Livewire::test(SeasonalCampaignManagerComponent::class)->call('toggle', $campaign->id);
        $this->assertFalse($campaign->fresh()->enabled);

        Livewire::test(SeasonalCampaignManagerComponent::class)->call('destroy', $campaign->id);
        $this->assertSame(0, SeasonalCampaign::count());
    }

    public function test_validation_rejects_bad_dates_colours_and_links(): void
    {
        Livewire::test(SeasonalCampaignManagerComponent::class)
            ->call('create')
            ->set('form.campaign_name', 'Bad')
            ->set('form.start_date', '2026-12-10')
            ->set('form.end_date', '2026-12-01')
            ->set('form.greeting_text_color', 'red')
            ->set('form.banner_link', 'javascript:alert(1)')
            ->set('form.timezone', 'Mars/Olympus')
            ->call('save')
            ->assertHasErrors(['form.end_date', 'form.greeting_text_color', 'form.banner_link', 'form.timezone']);

        $this->assertSame(0, SeasonalCampaign::count());
    }

    public function test_master_switch_persists_and_hides_active_campaign(): void
    {
        SeasonalCampaign::create(['enabled' => true, 'campaign_name' => 'Always on', 'timezone' => 'UTC']);
        $service = app(SeasonalCampaignService::class);
        $this->assertTrue($service->getPublicConfig()['enabled']);

        Livewire::test(SeasonalCampaignManagerComponent::class)->set('masterEnabled', false);

        $this->assertFalse($service->isMasterEnabled());
        $this->assertFalse($service->getPublicConfig()['enabled']);
    }

    public function test_countdown_requires_a_target_date_when_enabled(): void
    {
        Livewire::test(SeasonalCampaignManagerComponent::class)
            ->call('create')
            ->set('form.campaign_name', 'NYE')
            ->set('form.show_countdown', true)
            ->call('save')
            ->assertHasErrors(['form.countdown_target_date']);
    }
}
