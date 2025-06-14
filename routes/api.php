<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1/auth')
    ->middleware(['throttle:api'])
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('user.register;');
        Route::post('/login', [AuthController::class, 'login'])->name('user.login');
    });

Route::prefix('/v1')
    ->middleware(['throttle:api', 'auth:sanctum'])
    ->group(function () {
        Route::get('/auth/logout', [AuthController::class, 'logout'])->name('user.logout');
        Route::get('/auth/profile', fn(Request $request) => Auth::user());
        Route::apiResource('tags', TagController::class, )->except(['store', 'update', 'destroy']);
        Route::apiResource('posts', PostController::class);
    });

