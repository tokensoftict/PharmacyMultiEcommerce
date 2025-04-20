<?php

Route::prefix("general")->namespace("General")->group(function(){
    Route::get("/home", ["as" => "home.list", "uses" => "WholesalesHomeController"]);
});

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix("account")->namespace("Account")->group(function(){
        Route::post("/create-store", ["as" => "account.create-store", "uses" => "WholesalesRegistrationController"]);
    });
});



