<?php

use App\Http\Controllers\FeedbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/staffs', \App\Http\Controllers\Api\General\StaffController::class);