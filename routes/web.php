<?php

use App\Http\Controllers\Utilities\FileManagerController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;


Route::domain(config('app.MAIN_DOMAIN'))->middleware(['web'])->group(function (){
    Volt::route('/', 'pages.frontend.customer.index')->name('customer.index');
    //Route::get('/', [\App\Http\Controllers\HomePageController::class, 'index'])->name('customer.index');
    Route::get('file-manager', 'App\Http\Controllers\Utilities\FileManagerController@index')->name('file-manager.index');
    Route::prefix('sales-representative')->name('sales-representative.')->group(function (){
        Volt::route('{token}/accept-invitation', 'pages.salesrep.accept_invitation')->name('sales_rep.accept-invitation');
    });

    Route::prefix('administrator')->name('administrator.')->group(function (){
        Volt::route('{token}/accept-invitation', 'pages.administrator.accept_invitation')->name('admin.accept-invitation');
    });


    Route::get('{path}', [FileController::class, 'show'])->where('path', '.*')->name('assets.show');
});







