<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * ReferralRedirectController
 *
 * Handles incoming traffic to https://referral.generaldrugcentre.com/ref/{code}
 * and redirects users to the Detour deferred deep link:
 * https://psgdc.godetour.link/PrdRthERNv/ref/{code}
 */
class ReferralRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param string $code
     * @return RedirectResponse
     */
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $cleanCode = strtoupper(trim($code));

        $detourBaseUrl = rtrim(
            config('app.detour_referral_base_url', 'https://psgdc.godetour.link/PrdRthERNv/ref/'),
            '/'
        );

        $targetUrl = "{$detourBaseUrl}/{$cleanCode}";

        // Preserve any incoming query parameters
        if ($query = $request->getQueryString()) {
            $targetUrl .= "?{$query}";
        }

        return redirect()->away($targetUrl, 302, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
