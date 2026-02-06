<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('Jwt.Auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware('Jwt.Auth')->group(function () {
    Route::apiResource('orders', OrderController::class);
});

Route::middleware('Jwt.Auth')->group(function () {
    Route::apiResource('payments', PaymentController::class)
        ->only(['index', 'show']);

    Route::post('payments/process/{order}', [PaymentController::class, 'processPayment']);
});

Route::apiResource('gateways', PaymentGatewayController::class)
    ->only(['store', 'update']);
