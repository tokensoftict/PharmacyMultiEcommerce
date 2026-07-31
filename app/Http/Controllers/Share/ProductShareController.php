<?php

namespace App\Http\Controllers\Share;

use App\Http\Controllers\Controller;
use App\Services\Share\ProductShareService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Single-action controller that renders the public product sharing page.
 *
 * Routes:
 *   GET /wholesales/p/{slug}  → storeType = 'wholesales'
 *   GET /retail/p/{slug}      → storeType = 'retail'
 */
class ProductShareController extends Controller
{
    public function __construct(
        private readonly ProductShareService $shareService
    ) {}

    public function __invoke(Request $request, string $slug): Response|\Illuminate\Contracts\View\View
    {
        // Derive the store type from the first URI segment ('retail' or 'wholesales')
        $storeType = $request->segment(1); // e.g. "retail" or "wholesales"

        if (! in_array($storeType, ['retail', 'wholesales'], true)) {
            abort(404);
        }

        $viewModel = $this->shareService->resolve(
            slug: $slug,
            storeType: $storeType,
            currentUrl: $request->url()
        );

        if (! $viewModel) {
            abort(404);
        }

        $response = response()->view('share.product', compact('viewModel'));

        // ── HTTP Cache Headers ────────────────────────────────────────────────
        // Allow CDNs (Cloudflare, Nginx, etc.) to edge-cache this page for 1 hour.
        // "public" means both browser + proxy/CDN may cache it.
        // "s-maxage" is respected by CDNs; "max-age" by browsers.
        // "stale-while-revalidate" lets the CDN serve stale content while it
        //   fetches a fresh copy, preventing thundering-herd on popular products.
        $response->headers->set(
            'Cache-Control',
            'public, max-age=3600, s-maxage=3600, stale-while-revalidate=86400'
        );

        // ETag based on slug + store type — lets crawlers skip re-downloading
        // unchanged content using a conditional GET (304 Not Modified).
        $etag = md5("share-{$storeType}-{$slug}");
        $response->headers->set('ETag', "\"{$etag}\"");

        // Vary by User-Agent so bot crawls don't poison browser caches
        $response->headers->set('Vary', 'Accept-Encoding, User-Agent');

        return $response;
    }
}
