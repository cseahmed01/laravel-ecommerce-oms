<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Product routes - Admin and Vendor access
        Route::middleware('role:admin,vendor')->group(function () {
            Route::apiResource('products', ProductController::class);
            Route::post('products/{product}/variants', [ProductController::class, 'addVariant']);
            Route::put('products/{product}/variants/{variant}', [ProductController::class, 'updateVariant']);
            Route::delete('products/{product}/variants/{variant}', [ProductController::class, 'deleteVariant']);

            // Product search
            Route::get('products/search', [ProductController::class, 'search']);

            // Bulk import
            Route::post('products/import', [ProductController::class, 'import']);
            Route::get('products/import/{import}/status', [ProductController::class, 'importStatus']);
        });

        // Customer can view products and search
        Route::middleware('role:admin,vendor,customer')->group(function () {
            Route::get('products', [ProductController::class, 'index']);
            Route::get('products/{product}', [ProductController::class, 'show']);
            Route::get('products/search', [ProductController::class, 'search']);
        });
    });
});