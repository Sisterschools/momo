<?php

namespace Tests\Feature\Student;

use App\Models\School;
use App\Models\Student;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_creates_a_student_with_a_user_and_assigns_to_schools()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Storage::fake('public');

        // Create some schools
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        // Student data
        $studentData = [
            'name' => 'Jane Doe',
            'photo' => UploadedFile::fake()->image('student.jpg'),
            'grade' => 'Grade 10',
            'parent_contact' => '9876543210',
            'email' => 'janedoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'school_ids' => [$school1->id, $school2->id],
        ];

        // Send request to create student
        $response = $this->actingAs($admin)->postJson('/api/students', $studentData);

        // Assertions
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'janedoe@example.com', 'role' => 'student']);
        $this->assertDatabaseHas('students', ['name' => 'Jane Doe']);
        $this->assertDatabaseHas('school_student', ['student_id' => Student::first()->id, 'school_id' => $school1->id]);
        $this->assertDatabaseHas('school_student', ['student_id' => Student::first()->id, 'school_id' => $school2->id]);
    }

    public function test_admin_requires_valid_school_ids_when_creating_a_student()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Student data with invalid school_ids
        $studentData = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'school_ids' => [999], // Invalid school ID
        ];

        // Send request to create student
        $response = $this->actingAs($admin)->postJson('/api/students', $studentData);

        // Assertions
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('school_ids.0');
    }
}
