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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index']);
    Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show']);
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store']);

    Route::get('/comments/{comment}',[\App\Http\Controllers\CommentController::class, 'show']);
});
