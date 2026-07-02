<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AppUpdateMiddlewareTest extends TestCase
{
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
}
