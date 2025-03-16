<?php

use App\Http\Middleware\PermitTask;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'pages.index')->name('admin.dashboard');
Route::middleware(['web', 'auth', 'verified', PermitTask::class])->group(function(){
    Route::prefix('settings')->group(function(){
        Route::get('/system_settings', ['uses' => 'App\Livewire\Backend\Admin\Settings\SystemSettingsComponent'])->name('backend.admin.settings.system_settings');
        Route::get('/manufacturer', ['uses' => 'App\Livewire\Backend\Admin\Settings\ManufacturerComponentDataTable'])->name('backend.admin.settings.manufacturer');
        Route::get('/classification', ['uses' => 'App\Livewire\Backend\Admin\Settings\ClassificationComponentDataTable'])->name('backend.admin.settings.classification');
        Route::get('/productcategory', ['uses' => 'App\Livewire\Backend\Admin\Settings\ProductCategoryComponentDataTable'])->name('backend.admin.settings.product_category');
        Route::get('/productgroup', ['uses' => 'App\Livewire\Backend\Admin\Settings\ProductGroupComponentDataTable'])->name('backend.admin.settings.product_group');
        Route::get('/bank_account', ['uses' => 'App\Livewire\Backend\Admin\Settings\BankAccountComponentDataTable'])->name('backend.admin.settings.bank_account');
        Route::get('/payment_methods', ['uses' => 'App\Livewire\Backend\Admin\Settings\PaymentMethodDataTable'])->name('backend.admin.settings.payment_methods');
        Route::get('/delivery_methods', ['uses' => 'App\Livewire\Backend\Admin\Settings\DeliveryMethodDataTable'])->name('backend.admin.settings.delivery_methods');
        Volt::route('/payment_methods/{paymentMethod}/settings', 'backend.component.payment_method_settings')->name('backend.admin.settings.payment_methods.settings');
        Volt::route('/delivery_methods/{deliveryMethod}/settings', 'backend.component.delivery_method_settings')->name('backend.admin.settings.delivery_methods.settings');
        Route::get('/order_total', ['uses' => 'App\Livewire\Backend\Admin\Settings\OrderTotalComponentDataTable'])->name('backend.admin.settings.order_total');
        Route::get('/customer_group', ['uses' => 'App\Livewire\Backend\Admin\Settings\CustomerGroupComponentDataTable'])->name('backend.admin.settings.customer_group');
        Route::get('/customer_type', ['uses' => 'App\Livewire\Backend\Admin\Settings\CustomerTypeComponentDataTable'])->name('backend.admin.settings.customer_type');
        Route::get('/local_govt', ['uses' => 'App\Livewire\Backend\Admin\Settings\LocalGovtDataTable'])->name('backend.admin.settings.local_government');
        Route::get('/{localGovt}/{state}/address_and_town', ['uses' => 'App\Livewire\Backend\Admin\Settings\AddressAndTownDataTableComponent'])->name('backend.admin.settings.address_and_town');
        Route::get('/image_slider', ['uses' => 'App\Livewire\Backend\Admin\Settings\ImageSliderComponentDataTable'])->name('backend.admin.settings.image_slider');
        Route::get('/product_banner', ['uses' => 'App\Livewire\Backend\Admin\Settings\ProductBannerComponentDataTable'])->name('backend.admin.settings.product_banner');
        Route::get('/image_gallery', ['uses' => 'App\Livewire\Backend\Admin\Settings\ImageGalleryComponentDataTable'])->name('backend.admin.settings.image_gallery');
        Route::get('/town_and_distance', ['uses' => 'App\Livewire\Backend\Admin\Settings\TownAndDistanceDataTableComponent'])->name('backend.admin.settings.town_and_distance');
    });

    Route::prefix('stock_manager')->group(function(){
        Route::get('/list_stock', ['uses' => 'App\Livewire\Backend\Admin\Stock\StockManagerDataTableComponent'])->name('backend.admin.stock_manager.list_stock');
        Route::get('/{stock_id}/view', ['uses' => 'App\Livewire\Backend\Admin\Stock\ShowStock'])->name('backend.admin.stock_manager.view');
        Route::get('/stock_restriction', ['uses' => 'App\Livewire\Backend\Admin\Stock\StockRestrictionDataTable'])->name('backend.admin.stock_manager.stock_restriction');
        Route::get('/stock_size', ['uses' => 'App\Livewire\Backend\Admin\Stock\StockSizeDatatable'])->name('backend.admin.stock_manager.stock_size');
    });

    Route::prefix('customer_manager')->group(function(){
        Route::get('/{account}/view', ['uses' => 'App\Livewire\Backend\Admin\Customer\Wholesales\ShowCustomer'])->name('backend.admin.customer_manager.wholesales.view');
        Route::get('/search/history', ['uses' => 'App\Livewire\Backend\Admin\Customer\CustomerSearchHistoryDataTable'])->name('backend.admin.customer_manager.customer_search_history.list');
    });

    Route::prefix('sales_rep_manager')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\SalesRep\SalesRepManagerDataTable'])->name('backend.admin.sales_rep_manager.list');
        Route::get('/{salesRepresentative}/dashboard', ['uses' => 'App\Livewire\Backend\Admin\SalesRep\ShowDashboard'])->name('backend.admin.sales_rep_manager.view_report');
    });

    Route::prefix('push_notification')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\PushNotification\PushNotificationDatatable'])->name('backend.admin.push_notification.list');
        Route::get('/{pushNotification}/view', ['uses' => 'App\Livewire\Backend\Admin\PushNotification\ShowPushNotification'])->name('backend.admin.push_notification.view');
    });

    Route::prefix('coupon')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Coupon\CouponDatatable'])->name('backend.admin.coupon.list');
    });

    Route::prefix('voucher')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Voucher\VoucherDatatable'])->name('backend.admin.voucher.list');
    });

    Route::prefix('order')->group(function(){
        Route::get('/list', ['uses' => 'App\Livewire\Backend\Admin\Order\OrderDataTableComponent'])->name('backend.admin.order.list');
        Route::get('/report', ['uses' => 'App\Livewire\Backend\Admin\Order\OrderDataTableReportComponent'])->name('backend.admin.order.report');
        Route::get('/{order}/view', ['uses' => 'App\Livewire\Backend\Admin\Order\ShowOrder'])->name('backend.admin.order.view');
        Route::get('/{order}/edit', ['uses' => 'App\Livewire\Backend\Admin\Order\EditOrder'])->name('backend.admin.order.update');
        Route::get('/{order}/edit_product', ['uses' => 'App\Livewire\Backend\Admin\Order\EditOrderProduct'])->name('backend.admin.order.update_product');
    });

    Route::prefix('promotion')->group(function(){
        Route::get('/promotion', ['uses' => 'App\Livewire\Backend\Admin\Promotion\PromotionTableComponent'])->name('backend.admin.promotion.list');
    });

});
