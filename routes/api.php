<?php

use App\Api\Quotes\Controllers\QuoteController;
use App\Api\Ratings\Controllers\RatingController;
use App\Api\Users\Controllers\AuthController;
use App\Api\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('set.locale')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('v1')->group(function () {
            Route::apiResource('quotes', QuoteController::class);

            Route::get('me/quotes', [QuoteController::class, 'me'])
                ->name('me.quotes');
            Route::get('me', [UserController::class, 'me'])
                ->name('me');

            Route::apiResource('users', UserController::class)
                ->only(['index', 'show']);

            Route::apiResource('ratings', RatingController::class)
                ->only(['index', 'show', 'update', 'destroy']);

            Route::post('ratings/quotes/{quote}', [RatingController::class, 'store'])
                ->name('ratings.quotes.store');
            // Route::post('ratings/CanBeRated/{model}', [RatingController::class, 'store'])->name('...')
        });
    });

    Route::post('api-token-auth', [AuthController::class, 'login'])
        ->name('api-token-auth');

    Route::post('register', [AuthController::class, 'register'])
        ->name('register');
});
