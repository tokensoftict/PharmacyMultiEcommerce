<?php

use Illuminate\Support\Facades\Route;
Route::get("/", ['uses' => 'App\Http\Controllers\HomePageController@pushIndex']);
Route::post('manufacturers', ['as' => 'push.manufacturer' ,'uses' => 'App\Http\Controllers\Push\ManufacturerPushController']);
Route::post('classifications', ['as' => 'push.classification', 'uses' => 'App\Http\Controllers\Push\ClassificationPushController']);
Route::post('productcategories', ['as' => 'push.productcategories', 'uses' => 'App\Http\Controllers\Push\ProductCategoryPushController']);
Route::post('productgroups', ['as' => 'push.productgroups', 'uses' => 'App\Http\Controllers\Push\ProductGroupPushController']);
Route::post('stocks', ['as' => 'push.stocks', 'uses' => 'App\Http\Controllers\Push\StockPushController']);
Route::post('new_arrivals', ['as' => 'push.new_arrivals', 'uses' => 'App\Http\Controllers\Push\NewArrivalPushController']);
Route::post('existing_customer', ['as' => 'push.existing_customer', 'uses' => 'App\Http\Controllers\Push\CustomerPushController']);
