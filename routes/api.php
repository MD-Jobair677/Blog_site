<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::prefix('/user')->middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

    Route::get('/profile', 'profile');
    Route::put('/update/profile', 'updateProfile');
    Route::put('/password', 'updatePassword');
    Route::post('/logout', 'logout');
});









Route::prefix('/post')->middleware('auth:sanctum')->controller(PostController::class)->group(function () {

    Route::get('/all', 'index');
    Route::get('/{id}', 'showPostById');
    Route::post('/add', 'store');
    Route::put('/update/{id}', 'update');
    Route::delete('/post/{id}', 'destroy');
    Route::get('/search/{query}', 'search');
    // Route::get('/category/{categoryId}', 'getByCategory');
    Route::get('/tag/{tagId}', 'getByTag');

    Route::get('/get/user', 'getPostByUser');



});




Route::prefix('/tag')->middleware('auth:sanctum')->controller(TagController::class)->group(function () {
    Route::get('/all', 'index');
    Route::get('/{id}', 'showTagById');
    Route::post('/add', 'TagStore');
    Route::put('/update/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});
