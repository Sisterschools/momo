<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Http\Requests\Program\StoreProgramRequest;
use App\Http\Requests\Program\UpdateProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramCollection;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::paginate(10);
        return ProgramCollection::make($programs);
    }

    public function store(StoreProgramRequest $request)
    {
        $program = Program::create($request->validated());
        return ProgramResource::make($program);
    }

    public function show(Program $program)
    {
        return ProgramResource::make($program);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $program->update($request->validated());
        return ProgramResource::make($program);
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return response()->json(null, 204);
    }

    public function projects(Program $program)
    {
        return ProjectResource::collection($program->projects);

    }

    public function completedProjects(Program $program)
    {
        $completedProjects = $program->projects()->wherePivot('is_completed', true)->get();
        return ProjectResource::collection($completedProjects);
    }
}