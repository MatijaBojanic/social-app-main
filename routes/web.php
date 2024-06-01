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

    Route::get('/users/{user}', [\App\Http\Controllers\UsersController::class, 'show']);
    Route::get('/users', [\App\Http\Controllers\UsersController::class, 'index']);

    Route::get('followers', [\App\Http\Controllers\UsersController::class, 'followers']);
    Route::get('following', [\App\Http\Controllers\UsersController::class, 'following']);


    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index']);
    Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show']);
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store']);

    Route::get('/comments/{comment}',[\App\Http\Controllers\CommentController::class, 'show']);

    Route::post('/users/{user}/follow', [\App\Http\Controllers\UsersController::class, 'follow']);
});
