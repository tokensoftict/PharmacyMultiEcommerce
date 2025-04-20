<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix("sales_representative")->namespace("SalesRepresentatives")->group(function(){
        Route::get("/dashboard", ["as" => "dashboard", "uses" => "SalesRepresentativesDashboardController"]);
    });

    Route::prefix("order")->namespace("Order")->group(function(){
        Route::get("lists", ["as" => "order.lists", "uses" => "OrdersController"]);
        Route::get("{order}/show", ["as" => "order.show", "uses" => "OrderController"]);
    });
});
