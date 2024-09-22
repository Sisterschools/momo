<?

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Program;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Project\AttachStudentsToProgramRequest;
use App\Http\Requests\Project\AttachProgramProjectRequest;
use Illuminate\Http\JsonResponse;


class ProjectProgramController extends Controller
{
    use AuthorizesRequests;

    public function attachStudentsToProgram(
        AttachStudentsToProgramRequest $request,
        Project $project,
        Program $program
    ): JsonResponse {
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

    public function attach(AttachProgramProjectRequest $request, Project $project, Program $program): JsonResponse
    {
        $project->programs()->attach($program->id);
        return response()->json(['message' => 'Program attached to the project successfully.']);
    }

    public function detach(Project $project, Program $program): JsonResponse
    {
        $project->programs()->detach($program->id);
        return response()->json(['message' => 'Program detached from the project successfully.']);
    }

    public function markAsComplete(Project $project, Program $program): JsonResponse
    {
        $project->programs()->updateExistingPivot($program->id, [
            'is_completed' => true,
            'completed_at' => now(),
        ]);
        return response()->json(['message' => 'Program marked as complete in the project.']);
    }

    public function markAsIncomplete(Project $project, Program $program): JsonResponse
    {
        $project->programs()->updateExistingPivot($program->id, [
            'is_completed' => false,
            'completed_at' => null,
        ]);
        return response()->json(['message' => 'Program marked as incomplete in the project.']);
    }

}

