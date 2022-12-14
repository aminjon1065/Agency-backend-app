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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Auth\Authentification::class, 'register'])->name('user.register');
    Route::post('/login', [\App\Http\Controllers\Auth\Authentification::class, 'login'])->name('user.login')->name('user.login');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('v1')->group(function () {
        Route::get('isAuth', [\App\Http\Controllers\Auth\Authentification::class, 'isAuth']);
    });
});
