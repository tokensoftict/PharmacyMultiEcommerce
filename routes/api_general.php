<?php

use Illuminate\Support\Facades\Route;

Route::prefix("stock")->namespace("Stock")->group(function(){
    Route::get("{stock}/show", ["as" => "stock.show", "uses" => "StockController"]);
});

Route::prefix("pop-ups")->namespace("Popup")->group(function(){
    Route::get("list", ["as" => "pop-ups.list", "uses" => "CustomerSpecialPromotionController"]);
});

Route::prefix("general")->namespace("General")->group(function(){
    Route::get("/manufacturers", ["as" => "manufacturer.list", "uses" => "ManufacturerController"]);
    Route::get("/product_categories", ["as" => "product_categories.list", "uses" => "ProductCategoryController"]);
    Route::get("/product_manufacturers", ["as" => "product_categories.list", "uses" => "ProductManufacturerController"]);
    Route::get("/classifications", ["as" => "classifications.list", "uses" => "ClassificationController"]);
    Route::get("/productgroups", ["as" => "productgroups.list", "uses" => "ProductGroupController"]);
    Route::get("/countries", ["as" => "countries.list", "uses" => "CountriesController"]);
    Route::get("{country}/states", ["as" => "states.list", "uses" => "StatesController"]);
    Route::get("{state}/lgas", ["as" => "lgas.list", "uses" => "LocalGovernmentController"]);
    Route::get("{state}/towns", ["as" => "towns.list", "uses" => "TownController"]);

    Route::get("/payment_method", ["as" => "payment_method.list", "uses" => "PaymentMethodListController"]);
    Route::get("/delivery_method", ["as" => "delivery_method.list", "uses" => "DeliveryMethodListController"]);
});


Route::prefix("stock")->namespace("Stock")->group(function(){
    Route::get("/bestseller", ["as" => "stock.bestseller", "uses" => "BestSellerController"]);
    Route::get("/promo", ["as" => "stock.promo", "uses" => "PromoStockController"]);
    Route::get("/offer", ["as" => "stock.offer", "uses" => "SpecialOfferStockController"]);
    Route::get("/new-arrivals", ["as" => "stock.new-arrivals", "uses" => "NewArrivalsStockController"]);
    Route::get("/special-offers", ["as" => "stock.special-offers", "uses" => "SpecialOfferStockController"]);
    Route::get("/{classification}/by_classification", ["as" => "stock.by_classification", "uses" => "StockByProductClassificationController"]);
    Route::get("/{productcategory}/by_productcategory", ["as" => "stock.by_productcategory", "uses" => "StockByProductCategoriesController"]);
    Route::get("/{manufacturer}/by_manufacturer", ["as" => "stock.manufacturer", "uses" => "StockByProductManufacturerController"]);

});

Route::middleware(['auth:sanctum'])->group(function(){

    Route::prefix("general")->namespace("General")->group(function(){
        Route::get("/{deliveryMethod}/delivery_method", ["as" => "delivery_method.setDefault", "uses" => "SetDeliveryMethodAsDefaultDeliveryMethodController"]);
        Route::get("/{paymentMethod}/payment_method", ["as" => "payment_method.setDefault", "uses" => "SetPaymentMethodAsDefaultPaymentMethodController"]);
        Route::get("notifications", ["as" => "notifications", "uses" => "UsersNotificationController"]);
    });

    Route::prefix("address")->namespace("Address")->group(function(){
        Route::get("/{address}/show", ["as" => "address.get", "uses" => "AddressController"]);
        Route::get("/list", ["as" => "address.list", "uses" => "ListAddressesController"]);
        Route::post("/create", ["as" => "address.create", "uses" => "CreateNewAddressController"]);
        Route::post("/{address}/update", ["as" => "address.update", "uses" => "UpdateAddressController"]);
        Route::get("/{address}/remove", ["as" => "address.remove", "uses" => "RemoveAddressController"]);
        Route::get("/{address}/set_as_default", ["as" => "address.set_as_default", "uses" => "SetAddressAsDefaultCheckoutAddress"]);
    });

    Route::prefix("cart")->namespace("Cart")->group(function(){
        Route::post("/add-item", ["as" => "cart.add-item", "uses" => "AddItemToCartController"]);
        Route::get("{stock}/remove-item", ["as" => "cart.remove-item", "uses" => "RemoveItemFromCartController"]);
        Route::get("lists", ["as" => "cart.lists", "uses" => "ListItemsInCart"]);
        Route::get("clear", ["as" => "cart.clear", "uses" => "ClearAllItemsInCartController"]);
    });


    Route::prefix("wishlist")->namespace("Wishlist")->group(function(){
        Route::post("/add-item", ["as" => "wishlist.add-item", "uses" => "AddItemToWishlistController"]);
        Route::get("{stock}/remove-item", ["as" => "wishlist.remove-item", "uses" => "RemoveItemFromWishlistController"]);
        Route::get("lists", ["as" => "wishlist.lists", "uses" => "ListItemsInWishlist"]);
        Route::get("clear", ["as" => "wishlist.clear", "uses" => "ClearAllItemsInWishlistController"]);
    });


    Route::prefix("checkout")->namespace("Checkout")->group(function(){
        Route::get("payment_methods", ["as" => "checkout.payment_methods", "uses" => "PaymentMethodListController"]);
        Route::get("delivery_methods", ["as" => "checkout.delivery_methods", "uses" => "DeliveryMethodListController"]);

        Route::post("save_payment_method", ["as" => "checkout.save_payment_method", "uses" => "SavePaymentMethodController"]);
        Route::post("save_delivery_method", ["as" => "checkout.save_delivery_method", "uses" => "SaveDeliveryMethodController"]);

        Route::post("save_delivery_address_id", ["as" => "checkout.save_delivery_address_id", "uses" => "SaveDeliveryAddressController"]);
        Route::get('calculate_shopping_cart', ["as" => "checkout.calculate_shopping_cart", "uses" => "CalculateShoppingCartTotalController"]);

        Route::post("apply_coupon", ["as" => "checkout.apply_coupon", "uses" => "ApplyCouponCodeController"]);
        Route::get("remove_coupon", ["as" => "checkout.remove_coupon", "uses" => "RemoveCouponCodeController"]);

        Route::post("remove_order_total", ["as" => "checkout.remove_order_total", "uses" => "RemoveOrderTotalController"]);

        Route::get("create_transaction_log", ["as" => "checkout.create_transaction_log", "uses" => "PaymentGatewayTransactionLogsController"]);

        Route::get("confirm_order", ["as" => "checkout.confirm_order", "uses" => "ConfirmOrderController"]);
        Route::get("confirm_payment", ["as" => "checkout.confirm_payment", "uses" => "ConfirmPaymentController"]);

        Route::get("door_step_delivery_analysis", ["as" => "checkout.door_step_delivery_analysis", "uses" => "CalculateDoorStepDeliveryCalculation"]);
    });


    Route::prefix("order")->namespace("Order")->group(function(){
        Route::get("lists", ["as" => "order.lists", "uses" => "OrdersController"]);
        Route::get("{order}/show", ["as" => "order.show", "uses" => "OrderController"]);
    });

});
