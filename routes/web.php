<?php

use App\Web\Quotes\Controllers\QuoteController;
use App\Web\Users\Controllers\EmailUnsubscribeUsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::name('web.')->middleware('set.locale')->group(function () {
    Route::get('/', [QuoteController::class, 'index'])->name('welcome');

    Route::get('/email-unsubscribe-users/{userId}', EmailUnsubscribeUsersController::class)
        ->middleware('signed')
        ->name('email-unsubscribe-users');
});
