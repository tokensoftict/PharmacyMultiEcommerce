<?php

use App\Http\Middleware\PermitTask;

Route::middleware(['web', 'auth', 'verified', PermitTask::class])->group(function(){
    Route::prefix('customer_manager')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Customer\Wholesales\CustomerManagerDatatable'])->name('backend.admin.wholesales.customer_manager.list');
    });
});