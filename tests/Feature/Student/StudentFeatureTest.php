<?php

namespace Tests\Feature\Student;

use App\Models\Student;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StudentFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_students_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::factory()->create();

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('school-photo2.jpg');

        $studentData = [
            'name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'photo' => $file,
            'school_ids' => [$school->id],
        ];

        $this->actingAs($admin)->postJson('/api/students', $studentData);



        $response = $this->actingAs($admin)->getJson('/api/students');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'photo',
                        'email',
                        'school_ids',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'created_at',
                            'updated_at',
                        ],
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_admin_can_create_student()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('student-photo.jpg');

        $studentData = [
            'name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'photo' => $file,
            'school_ids' => [$school->id],
        ];

        $response = $this->actingAs($admin)->postJson('/api/students', $studentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'photo',
                    'email',
                    'school_ids',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'created_at',
                        'updated_at',
                    ],
                ]
            ]);

        $this->assertDatabaseHas('students', [
            'name' => 'Test Student',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'teststudent@example.com',
            'role' => 'student',
        ]);

        Storage::disk('public')->assertExists('photos/' . $file->hashName());
    }

    public function test_admin_can_update_student()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('updated-student-photo.jpg');

        $updateData = [
            'name' => 'Updated Student Name',
            'photo' => $file,
        ];

        $response = $this->actingAs($admin)->putJson("/api/students/{$student->id}", $updateData);

        // Assert the request was successful
        $response->assertStatus(200);



        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Student Name',
        ]);

        Storage::disk('public')->assertExists('photos/' . $file->hashName());
    }

    public function test_admin_can_delete_student_with_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();
        $user = User::factory()->create(['role' => 'student']);
        $student->user()->save($user);

        $response = $this->actingAs($admin)->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_can_search_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'student']);
        $user2 = User::factory()->create(['role' => 'student']);

        $student1 = Student::factory()->create(['name' => 'John Doe']);
        $student2 = Student::factory()->create(['name' => 'Jane Smith']);

        $student1->user()->save($user1);
        $student2->user()->save($user2);

        $response = $this->actingAs($admin)->getJson('/api/students/search?search=John');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'photo',
                        'email',
                        'school_ids',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'created_at',
                            'updated_at',
                        ],
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertJsonFragment(['name' => 'John Doe'])
            ->assertJsonMissing(['name' => 'Jane Smith']);
    }

    public function test_non_admin_cannot_create_student()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $school = School::factory()->create();

        $studentData = [
            'name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'school_ids' => [$school->id],
        ];

        $response = $this->actingAs($user)->postJson('/api/students', $studentData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('students', ['name' => 'Test Student']);
        $this->assertDatabaseMissing('users', ['email' => 'teststudent@example.com']);
    }

    public function test_cannot_create_student_with_invalid_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $studentData = [
            'name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'school_ids' => [999], // Non-existent school ID
        ];

        $response = $this->actingAs($admin)->postJson('/api/students', $studentData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['school_ids.0']);
    }
}