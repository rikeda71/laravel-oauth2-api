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

Route::prefix('auth')->middleware(['guest'])->group(function () {
    // google
    Route::get('/google', 'App\Http\Controllers\Auth\GoogleOauthController@getRedirectUrl')
        ->name('auth');
    Route::get('/google/callback', 'App\Http\Controllers\Auth\GoogleOauthController@callback')
        ->name('callback');

    // github
    Route::get('/github', 'App\Http\Controllers\Auth\GithubOauthController@getRedirectUrl')
        ->name('auth');
    Route::get('/github/callback', 'App\Http\Controllers\Auth\GithubOauthController@callback')
        ->name('callback');
});

// TODO: logoutを実装
Route::prefix('auth')->middleware(['web'])->get('/logout', 'App\Http\Controllers\Auth\LogoutController@logout');
