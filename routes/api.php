<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SchoolController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::get('unauthenticated', 'unauthenticated')->name('login');


});

// Route::post('/login', [AuthController::class, 'login']);



// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('register', [AuthController::class, 'register']);


    Route::post('/logout', [AuthController::class, 'logout']);

    Route::patch('/users/password', [AuthController::class, 'updatePassword']);
    Route::get('/users/roles', [UserController::class, 'getRoles']);

    // User routes
    Route::resource('users', UserController::class);

    // School routes 
    Route::resource('schools', SchoolController::class);



    // Teacher routes (to be implemented)
    Route::prefix('teachers')->group(function () {
        // Add teacher-related routes
    });

    // Student routes (to be implemented)
    Route::prefix('students')->group(function () {
        // Add student-related routes
    });
});