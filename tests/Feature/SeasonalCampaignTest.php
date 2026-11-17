<?php

namespace Tests\Feature;

use App\Models\SeasonalCampaign;
use App\Services\Seasonal\SeasonalCampaignService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeasonalCampaignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Legacy migrations contain MySQL-only SQL, so only the seasonal table is migrated
        // (in-memory sqlite, fresh per test).
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2026_11_17_090000_create_seasonal_campaigns_table.php',
            '--realpath' => false,
        ]);
        config(['cache.default' => 'array']);
        Cache::flush();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        \Illuminate\Support\Carbon::setTestNow();
        parent::tearDown();
    }

    private function freeze(string $when): void
    {
        $now = CarbonImmutable::parse($when, 'UTC');
        CarbonImmutable::setTestNow($now);
        \Illuminate\Support\Carbon::setTestNow($now);
    }

    private function make(array $attrs = []): SeasonalCampaign
    {
        return SeasonalCampaign::create(array_merge([
            'enabled' => true,
            'theme' => 'christmas',
            'campaign_name' => 'Christmas 2026',
            'start_date' => '2026-12-01',
            'end_date' => '2026-12-25',
            'timezone' => 'UTC',
            'priority' => 0,
            'show_snowfall' => true,
        ], $attrs));
    }

    private function config(): array
    {
        return $this->getJson('/api/app/seasonal-theme')->assertOk()->json('data');
    }

    public function test_returns_disabled_config_when_no_campaign_exists(): void
    {
        $this->getJson('/api/app/seasonal-theme')
            ->assertOk()
            ->assertJson(['status' => true, 'success' => true, 'data' => ['enabled' => false]]);
    }

    public function test_campaign_is_inactive_before_start_active_inside_and_expired_after_end(): void
    {
        $this->make();

        $this->freeze('2026-11-30 23:59:59');
        $this->assertFalse($this->config()['enabled']);

        Cache::flush();
        $this->freeze('2026-12-01 00:00:00');
        $this->assertTrue($this->config()['enabled']);

        Cache::flush();
        $this->freeze('2026-12-25 23:59:59'); // end date is inclusive
        $this->assertTrue($this->config()['enabled']);

        Cache::flush();
        $this->freeze('2026-12-26 00:00:00');
        $this->assertFalse($this->config()['enabled']);
    }

    public function test_schedule_is_evaluated_in_the_campaign_timezone(): void
    {
        // Lagos is UTC+1: Dec 1 00:00 Lagos == Nov 30 23:00 UTC
        $this->make(['timezone' => 'Africa/Lagos']);

        $this->freeze('2026-11-30 22:59:59');
        $this->assertFalse($this->config()['enabled']);

        Cache::flush();
        $this->freeze('2026-11-30 23:00:00');
        $this->assertTrue($this->config()['enabled']);
    }

    public function test_disabled_campaign_is_never_returned(): void
    {
        $this->make(['enabled' => false]);
        $this->freeze('2026-12-10 12:00:00');

        $this->assertFalse($this->config()['enabled']);
    }

    public function test_highest_priority_wins_when_campaigns_overlap(): void
    {
        $this->make(['campaign_name' => 'Low', 'priority' => 1]);
        $this->make(['campaign_name' => 'High', 'priority' => 10, 'theme' => 'new_year']);
        $this->freeze('2026-12-10 12:00:00');

        $data = $this->config();
        $this->assertSame('High', $data['campaign_name']);
        $this->assertSame('new_year', $data['theme']);
    }

    public function test_equal_priority_prefers_most_recently_started_campaign(): void
    {
        $this->make(['campaign_name' => 'Older', 'start_date' => '2026-12-01']);
        $this->make(['campaign_name' => 'Newer', 'start_date' => '2026-12-05']);
        $this->freeze('2026-12-10 12:00:00');

        $this->assertSame('Newer', $this->config()['campaign_name']);
    }

    public function test_master_switch_off_disables_everything(): void
    {
        $this->make();
        $this->freeze('2026-12-10 12:00:00');
        $this->assertTrue($this->config()['enabled']);

        app(SeasonalCampaignService::class)->setMasterEnabled(false);
        $this->assertFalse($this->config()['enabled']);

        app(SeasonalCampaignService::class)->setMasterEnabled(true);
        $this->assertTrue($this->config()['enabled']);
    }

    public function test_cache_is_invalidated_when_a_campaign_is_updated_or_deleted(): void
    {
        $campaign = $this->make(['greeting_message' => 'Merry Christmas!']);
        $this->freeze('2026-12-10 12:00:00');
        $this->assertSame('Merry Christmas!', $this->config()['greeting_message']);

        $campaign->update(['greeting_message' => 'Season\'s Greetings!']);
        $this->assertSame('Season\'s Greetings!', $this->config()['greeting_message']);

        $campaign->delete();
        $this->assertFalse($this->config()['enabled']);
    }

    public function test_payload_is_cached_between_requests(): void
    {
        $this->make();
        $this->freeze('2026-12-10 12:00:00');
        $this->config();

        \DB::enableQueryLog();
        $this->config();
        $this->assertCount(0, \DB::getQueryLog());
    }

    public function test_values_are_validated_and_sanitised(): void
    {
        $this->make([
            'snowfall_intensity' => 'extreme',
            'snowfall_speed' => 'warp',
            'snowfall_opacity' => 0.9,
            'greeting_message' => '<script>alert(1)</script>Merry <b>Christmas</b>',
            'greeting_background_color' => 'red; background:url(x)',
            'greeting_text_color' => '#FFF',
            'banner_link' => 'javascript:alert(1)',
            'greeting_alignment' => 'diagonal',
            'extra_effects' => ['lanterns' => true, 'Bad Key' => true, 'nested' => ['a']],
        ]);
        $this->freeze('2026-12-10 12:00:00');

        $data = $this->config();
        $this->assertSame('medium', $data['snowfall_intensity']);
        $this->assertSame('normal', $data['snowfall_speed']);
        $this->assertSame('alert(1)Merry Christmas', $data['greeting_message']);
        $this->assertNull($data['greeting_background_color']);
        $this->assertSame('#FFF', $data['greeting_text_color']);
        $this->assertNull($data['banner_link']);
        $this->assertSame('center', $data['greeting_alignment']);
        $this->assertSame(['lanterns' => true], $data['extra_effects']);
    }

    public function test_countdown_is_only_exposed_with_a_target_date(): void
    {
        $this->make([
            'theme' => 'new_year',
            'show_countdown' => true,
            'countdown_target_date' => null,
        ]);
        $this->freeze('2026-12-10 12:00:00');
        $this->assertFalse($this->config()['show_countdown']);

        SeasonalCampaign::query()->update(['countdown_target_date' => '2027-01-01 00:00:00']);
        Cache::flush();
        $data = $this->config();
        $this->assertTrue($data['show_countdown']);
        $this->assertSame('2027-01-01T00:00:00+00:00', $data['countdown_target_date']);
    }

    public function test_response_follows_existing_envelope_and_example_shape(): void
    {
        $this->make();
        $this->freeze('2026-12-10 12:00:00');

        $this->getJson('/api/app/seasonal-theme')
            ->assertOk()
            ->assertJsonStructure([
                'status', 'success', 'server_time',
                'data' => [
                    'enabled', 'theme', 'campaign_name', 'show_santa_hat', 'show_snowfall',
                    'snowfall_intensity', 'snowfall_speed', 'snowfall_opacity', 'show_decorations',
                    'show_christmas_lights', 'show_sparkles', 'show_ornaments', 'show_greeting',
                    'greeting_message', 'greeting_subtitle', 'show_seasonal_banner', 'banner_image',
                    'show_fireworks', 'show_confetti', 'show_countdown', 'start_date', 'end_date',
                ],
            ])
            ->assertJsonPath('data.start_date', '2026-12-01')
            ->assertJsonPath('data.end_date', '2026-12-25');
    }
}
