<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\ApiController;
use App\Services\Seasonal\SeasonalCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GET /api/app/seasonal-theme  (public, optionally user-aware)
 *
 * Returns the currently live seasonal campaign configuration for the requesting user (or guest),
 * or `{enabled:false}`. Resolves user identity from either a Sanctum bearer token or `?user_id=`.
 */
class SeasonalThemeController extends ApiController
{
    public function __invoke(Request $request, SeasonalCampaignService $service): JsonResponse
    {
        $userId = auth('sanctum')->id();
        if (! $userId && $request->filled('user_id') && is_numeric($request->query('user_id'))) {
            $parsed = (int) $request->query('user_id');
            $userId = $parsed > 0 ? $parsed : null;
        } else {
            $userId = $userId ? (int) $userId : null;
        }

        $cacheHeader = $userId !== null ? 'private, max-age=30' : 'public, max-age=60';

        return response()
            ->json([
                'status' => true,
                'success' => true,
                'data' => $service->getPublicConfig($userId),
                // Lets the app correct for device clock skew when running countdowns.
                'server_time' => now()->toIso8601String(),
            ])
            ->header('Cache-Control', $cacheHeader);
    }
}
