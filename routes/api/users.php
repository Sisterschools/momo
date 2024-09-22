<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::patch('/users/password', [AuthController::class, 'updatePassword']);
    Route::get('/users/roles', [UserController::class, 'getRoles']);
    Route::resource('users', UserController::class);
});
