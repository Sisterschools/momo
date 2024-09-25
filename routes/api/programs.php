<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProgramController;

Route::middleware('auth:sanctum')->group(function () {

    // Search programs
    Route::get('programs/search', [ProgramController::class, 'search'])
        ->name(name: 'programs.search');

    // Get all projects for a program
    Route::get('programs/{program}/projects', [ProgramController::class, 'projects'])
        ->name(name: 'programs.projects');

    Route::apiResource('programs', ProgramController::class);

});
