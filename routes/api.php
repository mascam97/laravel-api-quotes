<?php

use Illuminate\Http\Request;
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
        Route::apiResource('quotes', App\Http\Controllers\Api\V1\QuoteController::class);

        Route::apiResource('users', App\Http\Controllers\Api\V1\UserController::class)
            ->only(['index', 'show']);

        Route::get(
            'users/{user}/quotes',
            [
                App\Http\Controllers\Api\V1\UserController::class,
                'index_quotes'
            ]
        )->name('users.quotes.index');
    });
    Route::prefix('v2')->group(function () {
        Route::apiResource('quotes', App\Http\Controllers\Api\V2\QuoteController::class);

        Route::apiResource('users', App\Http\Controllers\Api\V2\UserController::class)
            ->only(['index', 'show']);

        Route::get(
            'users/{user}/quotes',
            [
                App\Http\Controllers\Api\V2\UserController::class,
                'index_quotes'
            ]
        )->name('users.quotes.index');
    });
});

Route::post('api-token-auth',  [
    App\Http\Controllers\Api\AuthController::class,
    'api_token_auth'
])->name('api-token-auth');

Route::post('register',  [
    App\Http\Controllers\Api\AuthController::class,
    'register'
])->name('register');
