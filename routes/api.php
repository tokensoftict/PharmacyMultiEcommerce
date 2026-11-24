<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/staffs', \App\Http\Controllers\Api\General\StaffController::class);

// Public, backend-controlled seasonal / festive configuration (Christmas, New Year, ...)
Route::get('/app/seasonal-theme', \App\Http\Controllers\Api\General\SeasonalThemeController::class)
    ->middleware(['throttle:60,1', \App\Http\Middleware\ForceJsonResponse::class])
    ->name('api.app.seasonal-theme');

// Public, backend-controlled Contact Us & Store Location configuration
Route::get('/app/contact-info', \App\Http\Controllers\Api\General\ContactInfoController::class)
    ->middleware(['throttle:60,1', \App\Http\Middleware\ForceJsonResponse::class])
    ->name('api.app.contact-info');

// OPay Offline / General Payment Webhook receiver
Route::post('/opay/webhook', \App\Http\Controllers\Api\Opay\OpayWebhookController::class)
    ->middleware([\App\Http\Middleware\ForceJsonResponse::class])
    ->name('api.opay.webhook');