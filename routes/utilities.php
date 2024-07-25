<?php

use App\Http\Middleware\PermitTask;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::domain(config("app.ADMIN_DOMAIN"))->group(function() {
    Route::middleware(['web', 'auth', 'verified', PermitTask::class])->group(function () {
        Route::prefix('utilities')->group(function (){
            Route::get('/stock/search', 'App\Http\Controllers\Utilities\Json\Stock\StockUtilitiesController@searchForStock')->name('utilities.stock.select2search');
            Route::get('/user/search', 'App\Http\Controllers\Utilities\Json\User\UserUtilitiesController@searchForUser')->name('utilities.stock.user_search');
        });
    });
});
