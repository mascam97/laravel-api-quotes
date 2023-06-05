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

Route::name('api.')->middleware('set.locale')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [UserController::class, 'me'])->name('me');
        Route::get('me/quotes', [QuoteController::class, 'me'])->name('me.quotes');

        Route::apiResource('users', UserController::class)->only(['index', 'show']);

        Route::apiResource('quotes', QuoteController::class);

        Route::apiResource('ratings', RatingController::class)
            ->only(['index', 'show', 'update', 'destroy']);
        Route::post('ratings/quotes/{quote}', [RatingController::class, 'store'])
            ->name('ratings.quotes.store')
            ->whereNumber('quote');
        // Route::post('ratings/CanBeRated/{model}', [RatingController::class, 'store'])->name('...')
    });
});
