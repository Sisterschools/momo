<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('students/search', [StudentController::class, 'search']);
    Route::resource('students', StudentController::class);
});
