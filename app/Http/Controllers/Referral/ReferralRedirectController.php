<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\ViewModels\ReferralShareViewModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * ReferralRedirectController
 *
 * Renders the rich social preview landing page and OpenGraph metadata when a user shares
 * their referral link (e.g. https://referral.generaldrugcentre.com/ref/{code}).
 */
class ReferralRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param string|null $code
     * @return Response
     */
    public function __invoke(Request $request, ?string $code = null): Response
    {
        $cleanCode = $code ? strtoupper(trim($code)) : '';

        // Find referrer user by referral code
        $referrer = null;
        if (!empty($cleanCode)) {
            $referrer = User::where('referral_code', $cleanCode)->first();
        }

        $viewModel = new ReferralShareViewModel(
            referralCode: $cleanCode,
            referrer: $referrer,
            currentUrl: $request->url()
        );

        $response = response()->view('referral.invite', compact('viewModel'));

        // Cache headers for CDNs and crawlers (5 min browser, 10 min CDN)
        $response->headers->set(
            'Cache-Control',
            'public, max-age=300, s-maxage=600, stale-while-revalidate=86400'
        );

        return $response;
    }
}

