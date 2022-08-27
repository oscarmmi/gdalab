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
Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('auth/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {    
    Route::get('find', [\App\Http\Controllers\AuthController::class, 'find'])->name('find');
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [\App\Http\Controllers\AuthController::class, 'refresh'])->name('refresh');
});


