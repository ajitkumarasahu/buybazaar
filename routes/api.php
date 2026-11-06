<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\UserProfileController;

// Laravel automatically adds the "/api" prefix, so these become /api/v1/...
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    // Public: confirms the API is alive.
    Route::get('/health', function () {
        return ApiResponse::success([
            'app'      => config('app.name'),
            'status'   => 'ok',
            'time_utc' => now()->utc()->toIso8601String(),
        ], 'BuyBazaar API is running.');
    });

    // ---------- Authentication ----------
    Route::prefix('auth')->group(function () {
        // Public
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

        // Requires a valid token AND an active account
        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // ---------- User profile & addresses ----------
    Route::prefix('users')->middleware(['auth:sanctum', 'active'])->group(function () {

        Route::get('/profile', [UserProfileController::class, 'show']);
        Route::put('/profile', [UserProfileController::class, 'update']);
        Route::put('/password', [UserProfileController::class, 'updatePassword'])->middleware('throttle:5,1');

        Route::get('/addresses', [AddressController::class, 'index']);
        Route::post('/addresses', [AddressController::class, 'store']);

        // ->can(...) runs AddressPolicy: users may only access their own addresses
        Route::get('/addresses/{address}', [AddressController::class, 'show'])->can('view', 'address');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->can('update', 'address');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->can('delete', 'address');
        Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault'])->can('update', 'address');
    });

});