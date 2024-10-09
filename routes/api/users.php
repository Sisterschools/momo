<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('register', [AuthController::class, 'register'])
        ->name('users.register');


    Route::post('/users/password/reset', [AuthController::class, 'passwordSetup'])
        ->name('password.update');


    Route::patch('/users/password', [AuthController::class, 'updatePassword'])
        ->name('users.password.update');
    Route::get('/users/roles', [UserController::class, 'getRoles'])
        ->name('users.roles');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');
});
