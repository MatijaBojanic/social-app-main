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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/users/{user}', [\App\Http\Controllers\UsersController::class, 'show']);
    Route::get('/users', [\App\Http\Controllers\UsersController::class, 'index']);

    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index']);
    Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show']);
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store']);

    Route::get('/comments/{comment}',[\App\Http\Controllers\CommentController::class, 'show']);
});
