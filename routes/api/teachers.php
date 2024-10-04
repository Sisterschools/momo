<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('teachers/search', [TeacherController::class, 'search'])
        ->name(name: 'teachers.search');
    ;
    Route::apiResource('teachers', TeacherController::class);
});
