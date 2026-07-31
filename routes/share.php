<?php

use App\Http\Controllers\Share\ProductShareController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Share Routes
|--------------------------------------------------------------------------
| These lightweight, public routes serve social-media sharing pages for
| individual products. They are intentionally stateless (no session, no
| Livewire) to minimise TTFB for social crawlers.
|
| Two store-type prefixes let us surface the correct price tier:
|   /wholesales/p/{slug}  → wholesales price
|   /retail/p/{slug}      → retail price
*/

Route::middleware('web')->group(function () {

    Route::prefix('wholesales')->name('share.wholesales.')->group(function () {
        Route::get('/p/{slug}', ProductShareController::class)
            ->name('product')
            ->where('slug', '[a-z0-9\-]+');
    });

    Route::prefix('retail')->name('share.retail.')->group(function () {
        Route::get('/p/{slug}', ProductShareController::class)
            ->name('product')
            ->where('slug', '[a-z0-9\-]+');
    });

});
