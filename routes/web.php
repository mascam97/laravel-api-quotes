<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome/index',[
        'users_count' => count(\App\Models\User::all()),
        'quotes_count' => count(\App\Models\Quote::all()),
        'ratings_count' => count(\App\Models\Rating::all())
    ]);
})->name('welcome');
