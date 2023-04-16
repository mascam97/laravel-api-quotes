<?php

use App\ApiAdmin\Activities\Controllers\ActivityController;
use App\ApiAdmin\Users\Controllers\AuthController;
use App\ApiAdmin\Users\Controllers\UserController;
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

Route::name('admin.')->middleware('set.locale')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [UserController::class, 'me'])
            ->name('me');

        Route::apiResource('users', UserController::class)
            ->only(['index', 'show', 'destroy']);

        Route::apiResource('activities', ActivityController::class)
            ->only(['index', 'show', 'destroy']);

        Route::post('activities/export', [ActivityController::class, 'export'])
            ->name('activities.export');
    });

    Route::post('api-token-auth', [AuthController::class, 'login'])
        ->name('api-token-auth');
});
