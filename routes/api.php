<?php

use App\Api\Profile\Controllers\ProfileController;
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
    Route::post('register', RegisterController::class)->name('register');

    Route::middleware('auth:api')->group(function () {
        Route::apiSingleton('profile', ProfileController::class)->destroyable();

        Route::get('me/quotes', [QuoteController::class, 'me'])->name('me.quotes');

        Route::apiResource('users', UserController::class)->only(['index', 'show']);

        Route::apiResource('quotes', QuoteController::class)->shallow();

        Route::apiResource('ratings', RatingController::class);
    });
});
