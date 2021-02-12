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
    return view('welcome');
});

Route::prefix('auth')->middleware('guest')->group(function() {
    // google
    Route::get('/{provider}', 'App\Http\Controllers\OauthController@auth')
        ->where('provider','google')
        ->name('auth');
    Route::get('/{provider}/callback', 'App\Http\Controllers\OauthController@callback')
        ->where('provider','google')
        ->name('callback');

    // github
    Route::get('/{provider}', 'App\Http\Controllers\OauthController@auth')
        ->where('provider','github')
        ->name('auth');
    Route::get('/{provider}/callback', 'App\Http\Controllers\OauthController@callback')
        ->where('provider','github')
        ->name('callback');
});
