<?php

namespace App\Http\Middleware;

use App\Services\AppUpdate\AppVersionService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * AppUpdateMiddleware
 * ─────────────────────────────────────────────────────────────────────────────
 * Automatically injects app update information into every mobile API response.
 *
 * Only activates when the request contains: device=mobile
 * Reads:  deviceType  (android|ios)
 *         version     (e.g. "1.14")
 *         versionCode (e.g. 114)
 *
 * Injects an `app_update` key into the JSON response body.
 * This middleware NEVER breaks the API — all exceptions are silently caught.
 *
 * Registration: add to each API route group in bootstrap/app.php
 */
class AppUpdateMiddleware
{
    public function __construct(private readonly AppVersionService $appVersionService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Only process mobile requests
        if ($request->query('device') !== 'mobile') {
            return $response;
        }

        try {
            $user = auth()->user() ?? auth('sanctum')->user();
            if ($user) {
                $platform = strtolower((string) $request->query('deviceType', $request->input('deviceType', 'android')));
                $version = (string) $request->query('version', $request->input('version', '1.0.0'));
                $versionCode = (int) $request->query('versionCode', $request->input('versionCode', 1));

                if ($user->device_type !== $platform || $user->version !== $version || (int) $user->version_code !== $versionCode) {
                    $user->update([
                        'device_type' => $platform,
                        'version' => $version,
                        'version_code' => $versionCode,
                    ]);

                    $user->updateLastSeen();
                } else {
                    $user->updateLastSeen();
                }
            }
        } catch (Throwable) {
            // Ignore database write failures to prevent breaking request lifecycle
        }

        try {
            $response = $this->injectUpdateInfo($request, $response);
        } catch (Throwable) {
            // Never let a version-check failure affect the actual response
        }

        return $response;
    }

    /**
     * Decode the JSON response, merge app_update, re-encode and return.
     */
    private function injectUpdateInfo(Request $request, Response $response): Response
    {
        // We can only inject into JSON responses
        if (!$response instanceof JsonResponse) {
            $contentType = $response->headers->get('Content-Type', '');
            if (!str_contains($contentType, 'application/json')) {
                return $response;
            }
        }

        $platform = strtolower((string) $request->query('deviceType', 'android'));
        $version = (string) $request->query('version', '1.0.0');
        $versionCode = (int) $request->query('versionCode', 1);

        // Normalise platform — only android or ios are valid
        if (!in_array($platform, ['android', 'ios'], true)) {
            $platform = 'android';
        }

        $updateInfo = $this->appVersionService->resolve($platform, $version, $versionCode);

        // Decode current body
        $body = json_decode($response->getContent(), true);

        if (!is_array($body)) {
            return $response; // Not a valid JSON object — leave untouched
        }

        $body['app_update'] = $updateInfo;

        // Re-encode and update response
        $response->setContent(json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // Ensure Content-Type header is correct
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
