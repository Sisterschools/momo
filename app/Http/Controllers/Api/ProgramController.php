<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Program\SearchProgramRequest;
use App\Models\Program;
use App\Http\Requests\Program\StoreProgramRequest;
use App\Http\Requests\Program\UpdateProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramCollection;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProgramController extends Controller
{
    use AuthorizesRequests; // Add this line to import the trait

    public function index()
    {

        $programs = Program::paginate();
        return ProgramCollection::make($programs);
    }

    public function store(StoreProgramRequest $request)
    {
        $this->authorize('create', Program::class);

        $program = Program::create($request->validated());
        return ProgramResource::make($program);
    }

    public function show(Program $program)
    {
        return ProgramResource::make($program);
    }

    public function update(UpdateProgramRequest $request, Program $program)
    {
        $this->authorize('update', $program);

        $program->update($request->validated());
        return ProgramResource::make($program);
    }

    public function destroy(Program $program)
    {
        $this->authorize('delete', $program);

        $program->delete();
        return response()->json(null, 204);
    }

    public function projects(Program $program)
    {

        // Paginate the projects related to the program (you can set the number of items per page)
        $projects = $program->projects()->paginate();
        // Return paginated ProjectResource collection
        return ProjectCollection::make($projects);


    }

    public function completedProjects(Program $program)
    {
        $completedProjects = $program->projects()
            ->wherePivot('is_completed', true)->paginate();

        return ProjectCollection::make($completedProjects);
    }

    public function search(SearchProgramRequest $request)
    {
        $term = $request->query('search');
        $programs = Program::search($term)->paginate()->appends(['search' => $term]);

        return ProgramCollection::make($programs);
    }


}