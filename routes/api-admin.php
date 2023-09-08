<?php

use App\ApiAdmin\Activities\Controllers\ActivityController;
use App\ApiAdmin\Profile\Controllers\ProfileController;
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

Route::name('admin.')->middleware(['set.locale', 'auth:api-admin'])->group(function () {
    /** Show, update and destroy the authenticated user */
    Route::apiSingleton('profile', ProfileController::class)->destroyable();

    /** Get, show and delete users */
    Route::apiResource('users', UserController::class)->except(['store', 'update']);

    /** Get, show and delete activities */
    Route::apiResource('activities', ActivityController::class)->except(['store', 'update']);

    /** Export activities */
    Route::post('activities/export', [ActivityController::class, 'export'])
        ->name('activities.export')
        ->middleware(['throttle:downloads']);
});
