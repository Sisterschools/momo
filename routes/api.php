<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController;
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
    Route::get('schools/search', [SchoolController::class, 'search']);
    Route::resource('schools', SchoolController::class);



    // Teacher routes
    Route::get('teachers/search', [TeacherController::class, 'search']);
    Route::resource('teachers', TeacherController::class);

    // Student routes
    Route::get('students/search', [StudentController::class, 'search']);
    Route::resource('students', StudentController::class);

});