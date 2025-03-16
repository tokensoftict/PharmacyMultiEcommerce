<?php
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use LivewireFilemanager\Filemanager\Http\Controllers\Files\FileController;


Route::domain(config('app.MAIN_DOMAIN'))->group(function (){
    Volt::route('/', 'pages.frontend.customer.index')->name('customer.index');
    Route::get('file-manager', 'App\Http\Controllers\Utilities\FileManagerController@index')->name('file-manager.index');
    Route::get('{path}', [FileController::class, 'show'])->where('path', '.*')->name('assets.show');
});







