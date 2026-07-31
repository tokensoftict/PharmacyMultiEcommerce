<?php

use App\Http\Controllers\Utilities\FileManagerController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;


/*
|--------------------------------------------------------------------------
| Product Share Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/share.php';

foreach (config('app.MAIN_DOMAIN', []) as $domain) {

    Route::domain($domain)->middleware(['web'])->group(function () {
        Route::view('/coupon-terms', 'coupon_terms')->name('coupon.terms');
        Volt::route('/', 'pages.frontend.customer.index')->name('customer.index');
        //Route::get('/', [\App\Http\Controllers\HomePageController::class, 'index'])->name('customer.index');
        Route::get('/app', function () {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            if (strpos($ua, 'android') !== false) {
                // Android → Play Store
                header("Location: https://play.google.com/store/apps/details?id=com.tokensoftict.psgdc");
                exit;

            } elseif (
                strpos($ua, 'iphone') !== false ||
                strpos($ua, 'ipad') !== false ||
                strpos($ua, 'ipod') !== false
            ) {
                // iOS → App Store
                header("Location: https://apps.apple.com/us/app/ps-gdc/id6741708076");
                exit;

            } else {
                // Fallback (optional)
                header("Location: https://generaldrugcentre.com/");
                exit;
            }
        })->name('customer.app_download');
        Route::get('file-manager', 'App\Http\Controllers\Utilities\FileManagerController@index')->name('file-manager.index');
        Route::prefix('sales-representative')->name('sales-representative.')->group(function () {
            Volt::route('{token}/accept-invitation', 'pages.salesrep.accept_invitation')->name('sales_rep.accept-invitation');
        });

        Route::prefix('administrator')->name('administrator.')->group(function () {
            Volt::route('{token}/accept-invitation', 'pages.administrator.accept_invitation')->name('admin.accept-invitation');
        });


        Route::get('{path}', [FileController::class, 'show'])->where('path', '.*')->name('assets.show');

    });
}







