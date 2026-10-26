<?php

use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

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

    // TEMPORARY: only used to test the 401 error format. We delete it in the next step.
    Route::get('/ping-protected', function () {
        return ApiResponse::success(null, 'You are authenticated.');
    })->middleware('auth:sanctum');

});