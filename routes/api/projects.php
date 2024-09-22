<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectProgramController;

Route::middleware('auth:sanctum')->group(function () {

    // Search projects
    Route::get('projects/search', [ProjectController::class, 'search'])
        ->name(name: 'projects.search');

    // attach program to project
    Route::post('projects/{project}/programs/{program}', [ProjectProgramController::class, 'attach'])
        ->name(name: 'projects.programs.attach');
    // detach program from project
    Route::delete('projects/{project}/programs/{program}', [ProjectProgramController::class, 'detach'])
        ->name(name: 'projects.programs.detach');
    // mark program in a project as complete
    Route::patch('projects/{project}/programs/{program}/complete', [ProjectProgramController::class, 'markAsComplete'])
        ->name(name: 'projects.programs.complete');
    // mark program in a project as incomplete
    Route::patch('projects/{project}/programs/{program}/incomplete', [ProjectProgramController::class, 'markAsIncomplete'])
        ->name(name: 'projects.programs.incomplete');

    // Get all programs for a project
    Route::get('projects/{project}/programs', [ProjectController::class, 'programs'])
        ->name(name: 'projects.programs');

    // Get completed programs for a project
    Route::get('projects/{project}/completed-programs', [ProjectController::class, 'completedPrograms'])
        ->name(name: 'projects.completed-programs');

    // Attach students to a project
    Route::post('projects/{project}/students', [ProjectController::class, 'attachStudentsToProject'])
        ->name(name: 'projects.students.attach');

    // Attach teachers to a project
    Route::post('projects/{project}/teachers', [ProjectController::class, 'attachTeachersToProject'])
        ->name(name: 'projects.teachers.attach');


    // Attach students to a program
    Route::post('projects/{project}/programs/{program}/students', [ProjectProgramController::class, 'attachStudentsToProgram'])
        ->name(name: 'projects.programs.students.attach');

    Route::apiResource('projects', ProjectController::class);

});
