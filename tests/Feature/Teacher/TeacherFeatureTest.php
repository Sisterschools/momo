<?php

namespace Tests\Feature\Teacher;

use App\Models\Teacher;
use App\Models\User;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TeacherFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_teachers_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::factory()->create();

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('teacher-photo.jpg');

        $teacherData = [
            'name' => 'Test Teacher',
            'email' => 'testteacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'photo' => $file,
            'phone_number' => '1234567890',
            'bio' => 'Test bio',
            'school_ids' => [$school->id],
        ];

        $this->actingAs($admin)->postJson('/api/teachers', $teacherData);

        $response = $this->actingAs($admin)->getJson('/api/teachers');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'photo',
                        'phone_number',
                        'bio',
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


    public function test_admin_can_create_teacher()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $school = School::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('teacher-photo.jpg');

        $teacherData = [
            'name' => 'Test Teacher',
            'email' => 'testteacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'photo' => $file,
            'phone_number' => '1234567890',
            'bio' => 'Test bio',
            'school_ids' => [$school->id],
        ];

        $response = $this->actingAs($admin)->postJson('/api/teachers', $teacherData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'photo',
                    'phone_number',
                    'bio',
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

        $this->assertDatabaseHas('teachers', [
            'name' => 'Test Teacher',
            'phone_number' => '1234567890',
            'bio' => 'Test bio',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testteacher@example.com',
            'role' => 'teacher',
        ]);

        Storage::disk('public')->assertExists('photos/' . $file->hashName());
    }

    public function test_admin_can_update_teacher()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = Teacher::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('updated-teacher-photo.jpg');

        $updateData = [
            'name' => 'Updated Teacher Name',
            'photo' => $file,
            'phone_number' => '9876543210',
            'bio' => 'Updated bio',
        ];

        $response = $this->actingAs($admin)->putJson("/api/teachers/{$teacher->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('teachers', [
            'id' => $teacher->id,
            'name' => 'Updated Teacher Name',
            'phone_number' => '9876543210',
            'bio' => 'Updated bio',
        ]);

        Storage::disk('public')->assertExists('photos/' . $file->hashName());
    }

    public function test_admin_can_delete_teacher_with_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = Teacher::factory()->create();
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher->user()->save($user);

        $response = $this->actingAs($admin)->deleteJson("/api/teachers/{$teacher->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_can_search_teachers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'teacher']);
        $user2 = User::factory()->create(['role' => 'teacher']);

        $teacher1 = Teacher::factory()->create(['name' => 'John Doe']);
        $teacher2 = Teacher::factory()->create(['name' => 'Jane Smith']);

        $teacher1->user()->save($user1);
        $teacher2->user()->save($user2);

        $response = $this->actingAs($admin)->getJson('/api/teachers/search?search=John');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'photo',
                        'phone_number',
                        'bio',
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

    public function test_non_admin_cannot_create_teacher()
    {
        $user = User::factory()->create(['role' => 'student']);
        $school = School::factory()->create();

        $teacherData = [
            'name' => 'Test Teacher',
            'email' => 'testteacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'school_ids' => [$school->id],
        ];

        $response = $this->actingAs($user)->postJson('/api/teachers', $teacherData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('teachers', ['name' => 'Test Teacher']);
        $this->assertDatabaseMissing('users', ['email' => 'testteacher@example.com']);
    }

    public function test_cannot_create_teacher_with_invalid_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $teacherData = [
            'name' => 'Test Teacher',
            'email' => 'testteacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
            'school_ids' => [999], // Non-existent school ID
        ];

        $response = $this->actingAs($admin)->postJson('/api/teachers', $teacherData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['school_ids.0']);
    }
}