<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $project;
    protected $program;
    protected $student;
    protected $admin;
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        // Create a project and a program
        $this->project = Project::factory()->create();
        $this->program = Program::factory()->create();
        $this->student = Student::factory()->create();

    }

    public function test_create_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $school = School::factory()->create();
        $school2 = School::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project.',
            'school_id_1' => $school->id,
            'school_id_2' => $school2->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'description' => 'This is a test project.',
        ]);
    }

    public function test_update_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create();

        $response = $this->actingAs($admin)->patchJson("/api/projects/{$project->id}", [
            'name' => 'Updated Project',
            'description' => 'This project has been updated.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
            'description' => 'This project has been updated.',
        ]);
    }

    public function test_delete_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_attach_teachers_to_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // Create a school
        $school = School::factory()->create();
        $school2 = School::factory()->create();

        // Create a teacher and associate it with the school
        $teacher = Teacher::factory()->create();
        $teacher->schools()->attach($school->id); // Ensure the teacher is linked to the school

        // Create a project and link it to the school
        $project = Project::factory()->create([
            'school_id_1' => $school->id,
            'school_id_2' => $school2->id, // Assuming both school fields are the same for this example
        ]);

        // Attach the teacher to the project
        $response = $this->actingAs($admin)->postJson("/api/projects/{$project->id}/teachers", [
            'teacher_ids' => [$teacher->id],
        ]);

        // Assert the response and database state
        $response->assertStatus(200)
            ->assertJson(['message' => 'Teachers attached to the project successfully.']);

        $this->assertDatabaseHas('project_teacher', [
            'project_id' => $project->id,
            'teacher_id' => $teacher->id,
        ]);
    }


    public function test_attach_student_to_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // Create a school
        $school = School::factory()->create();
        $school2 = School::factory()->create();

        // Create a Student and associate it with the school
        $student = Student::factory()->create();
        $student->schools()->attach($school->id); // Ensure the student is linked to the school

        // Create a project and link it to the school
        $project = Project::factory()->create([
            'school_id_1' => $school->id,
            'school_id_2' => $school2->id, // Assuming both school fields are the same for this example
        ]);


        $response = $this->actingAs($admin)->postJson("/api/projects/{$project->id}/students", [
            'student_ids' => [$student->id],
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Students attached to the project successfully.']);

        $this->assertDatabaseHas('project_student', [
            'project_id' => $project->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_attach_program_to_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create();
        $program = Program::factory()->create();

        $response = $this->actingAs($admin)->postJson("/api/projects/{$project->id}/programs/{$program->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Program attached to the project successfully.']);

        $this->assertDatabaseHas('program_project', [
            'project_id' => $project->id,
            'program_id' => $program->id,
        ]);
    }

    public function test_attach_students_to_program()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create the necessary records
        $project = Project::factory()->create();
        $program = Program::factory()->create();
        $students = Student::factory()->count(3)->create();

        // Attach students to the project
        $project->students()->attach($students->pluck('id')->toArray());

        // Attach the program to the project
        $project->programs()->attach($program->id);

        // Prepare the request data
        $data = [
            'student_ids' => $students->pluck('id')->toArray()
        ];

        // Make the POST request
        $response = $this->actingAs($admin)->postJson("/api/projects/{$project->id}/programs/{$program->id}/students", $data);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['message' => 'Students attached to program successfully.'])
            ->assertJsonStructure(['attached_student_count']);

        // Check that the students were attached to the program
        foreach ($students as $student) {
            $this->assertDatabaseHas('project_program_student', [
                'project_id' => $project->id,
                'program_id' => $program->id,
                'student_id' => $student->id,
            ]);
        }
    }


    public function test_updateProgramStatus()
    {
        // Create a project and program
        $project = Project::factory()->create();
        $program = Program::factory()->create();

        // Attach the program to the project with a default status
        $project->programs()->attach($program->id, ['status' => 'not ready']);

        // Prepare the request data for a valid status update
        $status = 'ready'; // or 'archived' / 'not ready' depending on the test case
        $response = $this->actingAs($this->admin)->patchJson(route('projects.programs.status', [$project->id, $program->id, $status]));

        // Assert the response status and content
        $response->assertStatus(200)
            ->assertJson(['message' => 'Program status updated successfully.']);

        // Verify the status in the pivot table
        $this->assertDatabaseHas('program_project', [
            'project_id' => $project->id,
            'program_id' => $program->id,
            'status' => $status,
        ]);
    }

    public function test_updateProgramStatus_invalid_status()
    {
        // Create a project and program
        $project = Project::factory()->create();
        $program = Program::factory()->create();

        // Attempt to update with an invalid status
        $invalidStatus = 'invalid_status'; // An invalid status
        $response = $this->actingAs($this->admin)->patchJson(route('projects.programs.status', [$project->id, $program->id, $invalidStatus]));

        // Assert the response status and content for the invalid status
        $response->assertStatus(422)
            ->assertJsonFragment(['errors' => ['status' => ['The status must be one of the following: not ready, ready, or archived.']]]);

    }



    public function test_getProgramsByStatus()
    {
        // Create a program and associated projects
        $program1 = Program::factory()->create(['name' => 'Math Program']);
        $program2 = Program::factory()->create(['name' => 'Science Program']);
        $program3 = Program::factory()->create(['name' => 'History Program']);

        $project = Project::factory()->create();

        // Attach the programs to the project with different statuses
        $project->programs()->attach($program1->id, ['status' => 'not ready']);
        $project->programs()->attach($program2->id, ['status' => 'ready']);
        $project->programs()->attach($program3->id, ['status' => 'archived']);

        // Test fetching programs by "not ready" status
        $response = $this->actingAs($this->admin)->getJson(route('projects.programs.by-status', ['project' => $project->id, 'status' => 'not ready']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Math Program']);

        // Test fetching programs by "ready" status
        $response = $this->actingAs($this->admin)->getJson(route('projects.programs.by-status', ['project' => $project->id, 'status' => 'ready']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Science Program']);

        // Test fetching programs by "archived" status
        $response = $this->actingAs($this->admin)->getJson(route('projects.programs.by-status', ['project' => $project->id, 'status' => 'archived']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'History Program']);

        // Test fetching programs with an invalid status
        $response = $this->actingAs($this->admin)->getJson(route('projects.programs.by-status', ['project' => $project->id, 'status' => 'invalid_status']));
        $response->assertStatus(422);
        $response->assertJsonFragment(['errors' => ['status' => ['The status must be one of the following: not ready, ready, or archived.']]]);
    }

}
