<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function(){

    Route::prefix("stock")->namespace("Stock")->group(function(){
        Route::get("/bestseller", ["as" => "stock.bestseller", "uses" => "BestSellerController"]);
        Route::get("/promo", ["as" => "stock.promo", "uses" => "PromoStockController"]);
        Route::get("/offer", ["as" => "stock.offer", "uses" => "SpecialOfferStockController"]);
        Route::get("/new-arrivals", ["as" => "stock.new-arrivals", "uses" => "NewArrivalsStockController"]);
        Route::get("/{manufacturer}/by_manufacturer", ["as" => "stock.by_manufacturer", "uses" => "StockByManufacturerController"]);
        Route::get("/{productcategory}/by_productcategory", ["as" => "stock.by_productcategory", "uses" => "StockByProductCategoriesController"]);
        Route::get("{stock}/show", ["as" => "stock.show", "uses" => "StockController"]);
    });

    Route::prefix("general")->namespace("General")->group(function(){
        Route::get("/manufacturers", ["as" => "manufacturer.list", "uses" => "ManufacturerController"]);
        Route::get("/product_categories", ["as" => "product_categories.list", "uses" => "ProductCategoryController"]);
        Route::get("/classifications", ["as" => "classifications.list", "uses" => "ClassificationController"]);
        Route::get("/productgroups", ["as" => "productgroups.list", "uses" => "ProductGroupController"]);
        Route::get("/countries", ["as" => "countries.list", "uses" => "CountriesController"]);
        Route::get("{country}/states", ["as" => "states.list", "uses" => "StatesController"]);
        Route::get("{state}/lgas", ["as" => "lgas.list", "uses" => "LocalGovernmentController"]);
        Route::get("{localGovt}/towns", ["as" => "towns.list", "uses" => "TownController"]);
        Route::get("/home", ["as" => "home.list", "uses" => "HomeController"]);
    });

    Route::prefix("address")->namespace("Address")->group(function(){
        Route::get("/list", ["as" => "address.list", "uses" => "ListAddressesController"]);
        Route::post("/create", ["as" => "address.create", "uses" => "CreateNewAddressController"]);
        Route::post("{address}/update", ["as" => "address.update", "uses" => "UpdateAddressController"]);
        Route::get("{address}/remove", ["as" => "address.remove", "uses" => "RemoveAddressController"]);
        Route::get("/set_as_default", ["as" => "address.set_as_default", "uses" => "SetAddressAsDefaultCheckoutAddress"]);
    });


    Route::prefix("cart")->namespace("Cart")->group(function(){
        Route::post("/add-item", ["as" => "cart.add-item", "uses" => "AddItemToCartController"]);
        Route::get("{stock}/remove-item", ["as" => "cart.remove-item", "uses" => "RemoveItemFromCartController"]);
        Route::get("lists", ["as" => "cart.lists", "uses" => "ListItemsInCart"]);
        Route::get("clear", ["as" => "cart.clear", "uses" => "ClearAllItemsInCartController"]);
    });

});
