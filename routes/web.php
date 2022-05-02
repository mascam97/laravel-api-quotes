<?php

use Domain\Quotes\Models\Quote;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Route;
use Support\Models\Rating;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    TODO: Create a controller that uses collections and more logic business
    return view('welcome/index', [
        'users_count' => count(User::all()),
        'quotes_count' => count(Quote::all()),
        'ratings_count' => count(Rating::all()),
    ]);
})->name('welcome');
