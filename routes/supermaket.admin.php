<?php

use App\Http\Middleware\PermitTask;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['web', 'auth', 'verified', PermitTask::class])->group(function(){
    Route::prefix("med-reminder")->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\MedReminder\MedReminderDataTableComponent'])->name('backend.admin.med_reminder.list');
    });

    Route::prefix('customer_manager')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Customer\Supermarket\CustomerManagerDatatable'])->name('backend.admin.supermarket.customer_manager.list');
    });
});

