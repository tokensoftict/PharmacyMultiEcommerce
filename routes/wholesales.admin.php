<?php

use App\Http\Middleware\PermitTask;

Route::middleware(['web', 'auth', 'verified', PermitTask::class])->group(function(){
    Route::prefix('customer_manager')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Customer\Wholesales\CustomerManagerDatatable'])->name('backend.admin.wholesales.customer_manager.list');
    });

    Route::prefix('stock_manager')->group(function(){
        Route::get('/stock_restriction', ['uses' => 'App\Livewire\Backend\Admin\Stock\StockRestrictionDataTable'])->name('backend.admin.stock_manager.stock_restriction');
        Route::get('{id}/view_stocks', ['uses' => 'App\Livewire\Backend\Admin\Stock\ViewStocksInStockRestrictionTableComponent'])->name('backend.admin.stock_manager.stock_restriction.view_stocks');
        Route::get('/stock_size', ['uses' => 'App\Livewire\Backend\Admin\Stock\StockSizeDatatable'])->name('backend.admin.stock_manager.stock_size');
    });
});