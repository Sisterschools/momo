<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('students/search', [StudentController::class, 'search'])
        ->name('students.search');
    ;
    Route::apiResource('students', StudentController::class);
});
