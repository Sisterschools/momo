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


    // List all students in a school
    Route::get('schools/{school}/students', [SchoolController::class, 'listStudents'])
        ->name('schools.students.list');

    // List all teachers in a school
    Route::get('schools/{school}/teachers', [SchoolController::class, 'listTeachers'])
        ->name('schools.teachers.list');

    Route::apiResource('schools', SchoolController::class);
});
