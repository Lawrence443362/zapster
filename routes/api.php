<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostContoller;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/v1/auth')
    ->middleware(['throttle:api'])
    ->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('user.register;');
        Route::post('login', [AuthController::class, 'login'])->name('user.login');
    });

Route::prefix('/v1')
    ->middleware(['throttle:api', 'auth:sanctum'])
    ->group(function () {
        Route::get('auth/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::apiResource('/tags', TagController::class);
        Route::apiResource('/posts', PostContoller::class);
    });

