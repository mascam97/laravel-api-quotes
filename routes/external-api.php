<?php

use App\ExternalApi\Quotes\Controllers\QuoteController;
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

Route::name('external-api.')->middleware(['set.locale', 'auth:external-api'])->group(function () {
    /** Get and show quotes as examples */
    Route::apiResource('quotes', QuoteController::class)->only(['index', 'show']);
});
