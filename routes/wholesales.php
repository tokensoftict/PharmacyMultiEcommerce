<?php

use Livewire\Volt\Volt;

foreach (config('app.WHOLESALES_DOMAIN', []) as $domain) {
    Route::domain($domain)->middleware(['web'])->group(function () {
        Volt::route('/', 'pages.frontend.customer.index')->name('customer.index');
    });
}