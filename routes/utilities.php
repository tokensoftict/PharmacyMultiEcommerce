<?php

use App\Http\Middleware\DetectApplicationEnvironment;
use App\Http\Middleware\PermitTask;
use Illuminate\Support\Facades\Route;


Route::domain(config("app.ADMIN_DOMAIN"))->group(function() {
    Route::middleware([DetectApplicationEnvironment::class ,'web', PermitTask::class])->group(function () {
        Route::prefix('utilities')->group(function (){
            Route::get('/stock/search', 'App\Http\Controllers\Utilities\Json\Stock\StockUtilitiesController@searchForStock')->name('utilities.stock.select2search');
            Route::get('/user/search', 'App\Http\Controllers\Utilities\Json\User\UserUtilitiesController@searchForUser')->name('utilities.user.search');
            Route::get('/wholesales_users/search', 'App\Http\Controllers\Utilities\Json\User\UserUtilitiesController@searchWholesalesCustomers')->name('utilities.user.wholesales.search');
        });
    });
});
