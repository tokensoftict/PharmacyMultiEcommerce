<?php
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::domain(config('app.MAIN_DOMAIN'))->group(function (){
    Volt::route('/', 'pages.frontend.customer.index')->name('customer.index');
});







