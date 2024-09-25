<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Program;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Project\AttachStudentsToProgramRequest;
use App\Http\Requests\Project\AttachProgramProjectRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Project\GetProgramsByStatusRequest;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\ProgramCollection;

class ProjectProgramController extends Controller
{
    use AuthorizesRequests;

    public function attachStudentsToProgram(
        AttachStudentsToProgramRequest $request,
        Project $project,
        Program $program
    ): JsonResponse {

        $this->authorize('create', Project::class);

        // Ensure the program is attached to the project
        $studentIds = $request->validated()['student_ids'];

        // prepare the the student ids with the program ids
        $data = array_map(fn($id) => ['student_id' => $id, 'program_id' => $program->id], $studentIds);

        // Attach the students to the project
        $project->programStudents($program)->syncWithoutDetaching($data);

        return response()->json([
            'message' => 'Students attached to program successfully.',
            'attached_student_count' => count($studentIds)
        ], 200);
    }

    // Attach a program to a project
    public function attach(AttachProgramProjectRequest $request, Project $project, Program $program): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project->programs()->attach($program->id);
        return response()->json(['message' => 'Program attached to the project successfully.']);
    }

    // Detach a program from a project
    public function detach(Project $project, Program $program): JsonResponse
    {
        $this->authorize('create', Project::class);

        $project->programs()->detach($program->id);
        return response()->json(['message' => 'Program detached from the project successfully.']);
    }

    // Mark a program with a specific status (ready, not ready, archived)
    public function updateProgramStatus(Project $project, Program $program, GetProgramsByStatusRequest $request)
    {
        // The validated status will be available here
        $status = $request->validated()['status'];

        // Update the program's status in the pivot table
        $project->programs()->updateExistingPivot($program->id, ['status' => $status]);

        return response()->json(['message' => 'Program status updated successfully.']);
    }



    /**
     * Get programs by status for a specific project
     */
    public function getProgramsByStatus(GetProgramsByStatusRequest $request, Project $project)
    {
        // The status is already validated and available in the request
        $status = $request->input('status');

        // Fetch programs based on the status
        $programs = $project->programs()->wherePivot('status', $status)->paginate();

        return ProgramCollection::make($programs);
    }

}

