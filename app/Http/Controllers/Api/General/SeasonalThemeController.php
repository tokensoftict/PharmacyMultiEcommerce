<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Services\Seasonal\SeasonalCampaignService;
use Illuminate\Http\JsonResponse;

/**
 * GET /api/app/seasonal-theme  (public)
 *
 * Returns the currently live seasonal campaign configuration, or `{enabled:false}`.
 * Follows the existing envelope (`status`, `data`) and also exposes `success` for new clients.
 */
class SeasonalThemeController extends ApiController
{
    public function __invoke(SeasonalCampaignService $service): JsonResponse
    {
        return response()
            ->json([
                'status' => true,
                'success' => true,
                'data' => $service->getPublicConfig(),
                // Lets the app correct for device clock skew when running countdowns.
                'server_time' => now()->toIso8601String(),
            ])
            // Short client/CDN cache; the server cache is invalidated on every admin change.
            ->header('Cache-Control', 'public, max-age=60');
    }
}

