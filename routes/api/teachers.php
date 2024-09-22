<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('teachers/search', [TeacherController::class, 'search']);
    Route::resource('teachers', TeacherController::class);
});
