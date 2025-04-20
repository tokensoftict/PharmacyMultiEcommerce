<?php

use App\Http\Middleware\PermitTask;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Middleware\LoginWithVerificationToken;

Route::domain(config('app.AUTH_DOMAIN'))->group(function (){
    Route::middleware([])->group(function () {
        Volt::route('/', 'pages.auth.login')->name('login');
        Volt::route('register', 'pages.auth.register')->name('register');
        Volt::route('forgot-password', 'pages.auth.forgot-password')->name('password.request');
        Volt::route('reset-password/{token}', 'pages.auth.reset-password')->name('password.reset');
        Volt::route('email-verified-successfully', 'pages.auth.verification-successful')->name('email.verification.successful');
    });

    Route::middleware([LoginWithVerificationToken::class])->group(function () {
        Volt::route('verify-email', 'pages.auth.verify-email')->name('verification.notice');
        Route::get('verify-email/{id}/{hash}', ['as' => 'verification.verify', 'uses' => 'App\Http\Controllers\Auth\VerifyEmailController'])->middleware(['signed', 'throttle:6,1']);
        Volt::route('confirm-password', 'pages.auth.confirm-password')->name('password.confirm');
    });
    Route::middleware(['auth', 'verified'])->group(function () {
        Volt::route('select-application', 'pages.auth.select-application')->name('select-application');
        Route::get('logout', 'App\Http\Controllers\Auth\LogoutController')->name('logout-web');
    });
});
