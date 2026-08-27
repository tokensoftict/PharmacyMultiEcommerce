<?php

use App\Http\Controllers\Referral\ReferralRedirectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Referral Subdomain & Redirect Routes
|--------------------------------------------------------------------------
| These lightweight, public routes handle referral link redirects for
| referral.generaldrugcentre.com.
|
| When a user visits:
|   https://referral.generaldrugcentre.com/ref/{code}
| The server redirects them to:
|   https://psgdc.godetour.link/PrdRthERNv/ref/{code}
| for deferred deep linking and app installation.
*/

Route::middleware('web')->group(function () {

    Route::get('/ref/{code}', ReferralRedirectController::class)
        ->name('referral.redirect')
        ->where('code', '[a-zA-Z0-9]+');

});
