<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppUpdateMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Register a temporary test route utilizing the middleware stack
        Route::middleware([
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\AppUpdateMiddleware::class,
        ])->get('/_test/mobile-api', function () {
            return response()->json([
                'status' => true,
                'data' => ['message' => 'hello']
            ]);
        });
    }

    /** @test */
    public function it_ignores_non_mobile_requests()
    {
        $response = $this->getJson('/_test/mobile-api');

        $response->assertStatus(200);
        $response->assertJsonMissing(['app_update']);
    }

    /** @test */
    public function it_injects_update_info_for_mobile_requests()
    {
        AppVersion::create([
            'app_type'       => 'android',
            'version_name'   => '1.15.0',
            'version_code'   => 115,
            'force_update'   => false,
            'update_message' => 'Test message',
            'store_url'      => 'https://example.com',
            'is_active'      => true,
        ]);

        $response = $this->getJson('/_test/mobile-api?device=mobile&deviceType=android&version=1.14.0&versionCode=114');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'app_update' => [
                'has_update',
                'force_update',
                'latest_version',
                'latest_version_code',
                'current_version',
                'current_version_code',
                'store_url',
                'update_message',
            ]
        ]);

        $this->assertTrue($response->json('app_update.has_update'));
        $this->assertFalse($response->json('app_update.force_update'));
    }

    /** @test */
    public function it_automatically_updates_the_latest_version_if_client_code_is_higher()
    {
        AppVersion::create([
            'app_type'       => 'android',
            'version_name'   => '1.14.0',
            'version_code'   => 114,
            'force_update'   => false,
            'update_message' => 'Old message',
            'store_url'      => 'https://example.com',
            'is_active'      => true,
        ]);

        $response = $this->getJson('/_test/mobile-api?device=mobile&deviceType=android&version=1.15.0&versionCode=115');

        $response->assertStatus(200);

        // Verify that in the response, the latest version has been auto-updated to match the client's version
        $this->assertFalse($response->json('app_update.has_update'));
        $this->assertEquals(115, $response->json('app_update.latest_version_code'));
        $this->assertEquals('1.15.0', $response->json('app_update.latest_version'));

        // Verify that it is written to the database as the active configuration
        $this->assertDatabaseHas('app_versions', [
            'app_type'     => 'android',
            'version_name' => '1.15.0',
            'version_code' => 115,
            'is_active'    => true,
        ]);

        // Verify that the old version is deactivated
        $this->assertDatabaseHas('app_versions', [
            'app_type'     => 'android',
            'version_code' => 114,
            'is_active'    => false,
        ]);
    }

    /** @test */
    public function it_updates_user_version_details_if_authenticated()
    {
        $user = \App\Models\User::factory()->create([
            'device_type'  => null,
            'version'      => null,
            'version_code' => null,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/_test/mobile-api?device=mobile&deviceType=android&version=1.14.0&versionCode=114');

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('android', $user->device_type);
        $this->assertEquals('1.14.0', $user->version);
        $this->assertEquals(114, $user->version_code);
    }
}
