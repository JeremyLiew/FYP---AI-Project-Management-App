<?php

use App\Http\Controllers\Web\{
    ContactUsController,
    ProductController,
    OrderController,
};

use App\Http\Controllers\Auth\{
    AuthController
};
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('general')->group(function () {
        Route::post('/contact-us', [ContactUsController::class, 'sendEmail']);
    });

    Route::prefix('products')->group(function () {
        Route::post('/list', [ProductController::class, 'productList']);
        Route::post('/info/{id}', [ProductController::class, 'productInfo']);
    });

});

Route::prefix('api')->middleware([])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

// sanctum route
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::delete('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });

    Route::prefix('general')->group(function () {
        Route::get('/order-history', [OrderController::class, 'orders']);
    });

    Route::prefix('products')->group(function () {
        Route::post('/checkout', [ProductController::class, 'checkout']);
    });
});

Route::get('password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
