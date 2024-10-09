<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SchoolController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('schools/search', [SchoolController::class, 'search'])
        ->name(name: 'schools.search');

    // Attach students to a school
    Route::post('schools/{school}/students', [SchoolController::class, 'attachStudentsToSchool'])
        ->name('schools.students.attach');

    // Attach teachers to a school
    Route::post('schools/{school}/teachers', [SchoolController::class, 'attachTeachersToSchool'])
        ->name('schools.teachers.attach');

    Route::apiResource('schools', SchoolController::class);
});
