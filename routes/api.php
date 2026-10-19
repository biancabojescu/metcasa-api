<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\ListingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::post('password/forgot', [PasswordResetController::class, 'forgot'])
            ->middleware('throttle:6,1')
            ->name('password.forgot');
        Route::post('password/reset', [PasswordResetController::class, 'reset'])
            ->middleware('throttle:6,1')
            ->name('password.reset');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])->name('me');
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('email/resend', [EmailVerificationController::class, 'resend'])
                ->middleware('throttle:6,1')
                ->name('email.resend');
            Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
        });
    });

    // Favorites - auth required
    Route::middleware('auth:sanctum')->prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('{listing}', [FavoriteController::class, 'store'])->name('store');
        Route::delete('{listing}', [FavoriteController::class, 'destroy'])->name('destroy');
        Route::delete('/', [FavoriteController::class, 'clear'])->name('clear');
    });

    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Listings - public read, protected write
    Route::prefix('listings')->name('listings.')->group(function () {
        Route::get('/', [ListingController::class, 'index'])->name('index');
        Route::get('{id}', [ListingController::class, 'show'])->whereNumber('id')->name('show');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/', [ListingController::class, 'store'])->name('store');
            Route::post('{listing}', [ListingController::class, 'update'])->name('update');
            Route::delete('{listing}', [ListingController::class, 'destroy'])->name('destroy');
        });
    });
});
