<?php

use App\ExternalApi\Quotes\Controllers\QuoteController;
use App\ExternalApi\Users\Controllers\AuthController;
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

Route::name('external-api.')->middleware('set.locale')->group(function () {
    Route::post('token-auth', [AuthController::class, 'login'])->name('token-auth');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('quotes', QuoteController::class)->only(['index', 'show']);
    });
});
