<?php

namespace Tests\Feature\Teacher;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_creates_a_teacher_with_a_user_and_assigns_to_schools()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('public');

        // Create some schools
        $school1 = School::factory()->create();
        $school2 = School::factory()->create();

        // Teacher data
        $teacherData = [
            'name' => 'John Doe',
            'photo' => UploadedFile::fake()->image('teacher.jpg'),
            'phone_number' => '1234567890',
            'bio' => 'Experienced Math Teacher',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'school_ids' => [$school1->id, $school2->id],
        ];

        // Send request to create teacher
        $response = $this->actingAs($admin)->postJson('/api/teachers', $teacherData);

        // Assertions
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com', 'role' => 'teacher']);
        $this->assertDatabaseHas('teachers', ['name' => 'John Doe']);
        $this->assertDatabaseHas('school_teacher', ['teacher_id' => Teacher::first()->id, 'school_id' => $school1->id]);
        $this->assertDatabaseHas('school_teacher', ['teacher_id' => Teacher::first()->id, 'school_id' => $school2->id]);
    }

    public function test_admin_requires_valid_school_ids_when_creating_a_teacher()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // Teacher data with invalid school_ids
        $teacherData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'school_ids' => [999], // Invalid school ID
        ];

        // Send request to create teacher
        $response = $this->actingAs($admin)->postJson('/api/teachers', $teacherData);

        // Assertions
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('school_ids.0');
    }
}
