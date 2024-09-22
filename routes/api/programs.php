<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProgramController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('programs', ProgramController::class);

    // Get all projects for a program
    Route::get('programs/{program}/projects', [ProgramController::class, 'projects']);

    // Get projects where a program is completed
    Route::get('programs/{program}/completed-projects', [ProgramController::class, 'completedProjects']);
});
