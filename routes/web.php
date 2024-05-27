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

Route::post('/login', [\App\Http\Controllers\AuthenticationController::class, 'login']);
Route::get('/logout', [\App\Http\Controllers\AuthenticationController::class, 'logout']);
Route::post('/register', [\App\Http\Controllers\AuthenticationController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/initialize', [\App\Http\Controllers\AuthenticationController::class, 'initialize']);
});
