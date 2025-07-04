<?php

use App\Http\Middleware\DetectApplicationEnvironment;
use Illuminate\Support\Facades\Route;

Route::prefix("account")->namespace("Account")->group(function(){
    Route::post("/login", ["as" => "account.login", "uses" => "LoginController"]);
    Route::post("/signup", ['as' => 'account.signup', 'uses' => "SignupController"]);
    Route::post("/forgot-password", ['as' => 'account.forgot-password', 'uses' => "ForgotPasswordController"]);
    Route::post("/reset-password", ['as' => 'account.reset-password', 'uses' => "ResetPasswordController"]);
    Route::post("/verify-phone", ['as' => 'account.verify-phone', 'uses' => "PhoneNumberVerificationController"])->middleware(['auth:sanctum']);
    Route::post("/verify-email", ['as' => 'account.verify-email', 'uses' => "EmailAddressVerificationController"])->middleware(['auth:sanctum']);
    Route::post("/resend-verification", ['as' => 'account.resend-verification', 'uses' => "ResendVerificationController"])->middleware(['auth:sanctum']);
    Route::get("/my-qrcode", ["as" => "account.my-qrcode", "uses" => "QRCodeController"]);
    Route::get("/logout", ["as" => "account.logout", "uses" => "LogoutController"])->middleware(['auth:sanctum']);
    Route::get("/me", ["as" => "account.me", "uses" => "MeController"])->middleware(['auth:sanctum']);
    Route::get("delete-my-account", ["as" => "account.delete-my-account", "uses" => "DeleteMyAccountController"]);
    Route::get("restore-my-account", ["as" => "account.restore-my-account", "uses" => "RestoreMyAccountController"]);
    Route::get("frontend-apps", ["as" => "account.frontend-apps", "uses" => "FrontEndAppsController"]);

    Route::middleware(['auth:sanctum'])->group(function(){
        Route::post("/change-password", ["as" => "account.change-password", "uses" => "ChangePasswordController"]);
    });
});


