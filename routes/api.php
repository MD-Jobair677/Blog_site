<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::prefix('/user')->middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

    Route::get('/profile', 'profile');
    Route::put('/update/profile', 'updateProfile');
    Route::put('/password', 'updatePassword');
    Route::post('/logout', 'logout');
});









Route::prefix('/api/post')->controller(PostController::class)->group(function () {

    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
    Route::get('/search/{query}', 'search');
    Route::get('/category/{categoryId}', 'getByCategory');
    Route::get('/tag/{tagId}', 'getByTag');
});




Route::prefix('/api/tag')->controller(TagController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'TagStore');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});
