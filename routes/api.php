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

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('quotes', App\Http\Controllers\Api\V1\QuoteController::class);
    
    Route::get(
        'users/{user_id}/quotes',
        [
            App\Http\Controllers\Api\V1\UserController::class,
            'index_quotes'
        ]
    )->name('user.quotes');
});

Route::post('login',  [
    App\Http\Controllers\Api\AuthController::class,
    'login'
])->name('login');

Route::post('register',  [
    App\Http\Controllers\Api\AuthController::class,
    'register'
])->name('register');
