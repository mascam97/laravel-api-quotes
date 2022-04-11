<?php

use App\Api\Controllers\Api\AuthController;
use App\Api\Controllers\Api\V1\QuoteController;
use App\Api\Controllers\Api\V1\UserController;
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

        Route::post('quotes/{quote}/rate', [QuoteController::class, 'rate']);
    });
});

Route::post('api-token-auth', [AuthController::class, 'api_token_auth'])
    ->name('api-token-auth');

Route::post('register', [AuthController::class, 'register'])
    ->name('register');
