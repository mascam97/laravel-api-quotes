<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V2\QuoteController as QuoteControllerV2;
use App\Http\Controllers\Api\V2\UserController as UserControllerV2;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('quotes', QuoteController::class);

        Route::apiResource('users', UserController::class)
            ->only(['index', 'show']);

        Route::get('users/{user}/quotes', [UserController::class, 'index_quotes'])
            ->name('users.quotes.index');
    });

    Route::prefix('v2')->group(function () {
        Route::apiResource('quotes', QuoteControllerV2::class);

        Route::post('quotes/{quote}/rate', [QuoteControllerV2::class, 'rate']);

        Route::apiResource('users', UserControllerV2::class)
            ->only(['index', 'show']);

        Route::get('users/{user}/quotes', [UserControllerV2::class, 'index_quotes'])
            ->name('users.quotes.index');
    });
});

Route::post('api-token-auth', [AuthController::class, 'api_token_auth'])
    ->name('api-token-auth');

Route::post('register', [AuthController::class, 'register'])
    ->name('register');
