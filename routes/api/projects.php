<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectProgramController;

Route::middleware('auth:sanctum')->group(function () {

    // Search projects
    Route::get('projects/search', [ProjectController::class, 'search'])
        ->name('projects.search');

    // Attach a program to a project
    Route::post('projects/{project}/programs/{program}', [ProjectProgramController::class, 'attach'])
        ->name('projects.programs.attach');

    // Detach a program from a project
    Route::delete('projects/{project}/programs/{program}', [ProjectProgramController::class, 'detach'])
        ->name('projects.programs.detach');

    // Mark a program with a specific status (ready, not_ready, archived)
    Route::patch('projects/{project}/programs/{program}/status/{status}', [ProjectProgramController::class, 'updateProgramStatus'])
        ->name('projects.programs.status');


    // List all programs for a project
    Route::get('projects/{project}/programs', [ProjectController::class, 'programs'])
        ->name('projects.programs');

    // Attach students to a project
    Route::post('projects/{project}/students', [ProjectController::class, 'attachStudentsToProject'])
        ->name('projects.students.attach');

    // Attach teachers to a project
    Route::post('projects/{project}/teachers', [ProjectController::class, 'attachTeachersToProject'])
        ->name('projects.teachers.attach');

    // Attach students to a program within a project
    Route::post('projects/{project}/programs/{program}/students', [ProjectProgramController::class, 'attachStudentsToProgram'])
        ->name('projects.programs.students.attach');

    // Get programs by status for a specific project
    Route::get('projects/{project}/programs/status/{status}', [ProjectProgramController::class, 'getProgramsByStatus'])
        ->name('projects.programs.by-status');

    // Resource routes for ProjectController (index, show, store, update, destroy)
    Route::apiResource('projects', ProjectController::class);
});
