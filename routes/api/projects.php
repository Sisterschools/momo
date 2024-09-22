<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectProgramController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('projects', ProjectController::class);

    // Custom routes for Project-Program relationship
    Route::post('projects/{project}/programs/{program}', [ProjectProgramController::class, 'attach']);
    Route::delete('projects/{project}/programs/{program}', [ProjectProgramController::class, 'detach']);
    Route::patch('projects/{project}/programs/{program}/complete', [ProjectProgramController::class, 'markAsComplete']);
    Route::patch('projects/{project}/programs/{program}/incomplete', [ProjectProgramController::class, 'markAsIncomplete']);

    // Get all programs for a project
    Route::get('projects/{project}/programs', [ProjectController::class, 'programs']);

    // Get completed programs for a project
    Route::get('projects/{project}/completed-programs', [ProjectController::class, 'completedPrograms']);

    // Attach students to a project
    Route::post('projects/{project}/students', [ProjectController::class, 'attachStudentsToProject']);

    // Attach teachers to a project
    Route::post('projects/{project}/teachers', [ProjectController::class, 'attachTeachersToProject']);


    // Attach students to a program
    Route::post('projects/{project}/programs/{program}/students', [ProjectProgramController::class, 'attachStudentsToProgram']);
});
