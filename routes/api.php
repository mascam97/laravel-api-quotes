<?php

use App\Api\Profile\Controllers\ProfileController;
use App\Api\PublicQuotes\Controllers\PublicQuotesController;
use App\Api\Quotes\Controllers\QuoteController;
use App\Api\Ratings\Controllers\RatingController;
use App\Api\Users\Controllers\RegisterController;
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
    /** Register a new user */
    Route::post('register', RegisterController::class)->name('register');

    Route::middleware('auth:api')->group(function () {
        /** Show, update and destroy the authenticated user */
        Route::apiSingleton('profile', ProfileController::class)->destroyable();

        /** Get and show users */
        Route::apiResource('users', UserController::class)->only(['index', 'show']);

        /** Get, show, create, update and destroy my quotes */
        Route::apiResource('quotes', QuoteController::class)->shallow();

        Route::name('public.')->prefix('public')->group(function () {
            /** Get and show quotes */
            Route::apiResource('quotes', PublicQuotesController::class)->only(['index', 'show']);
        });

        /** Get and show ratings, and create, update and destroy my ratings */
        Route::apiResource('ratings', RatingController::class);
    });
});
