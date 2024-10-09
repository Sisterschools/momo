<?php

namespace Tests\Feature\School;

use App\Models\School;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SchoolFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_schools_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('school-photo2.jpg');

        // Prepare the school data with a fake file
        $schoolData = [
            'title' => 'Test School',
            'address' => '123 Test St',
            'description' => 'This is a test school',
            'phone_number' => '555-1234',
            'website' => 'http://testschool.com',
            'founding_year' => 2000,
            'student_capacity' => 500,
            'photo' => $file, // Pass the fake file as the photo
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'school',
        ];




        // Create the school with the associated user
        $this->actingAs($admin)->postJson(
            '/api/schools',
            $schoolData
        );

        $this->actingAs($admin)->getJson('/api/schools')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'address',
                        'description',
                        'phone_number',
                        'website',
                        'founding_year',
                        'student_capacity',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'created_at',
                            'updated_at',
                        ],
                    ]
                ]
            ]);
    }

    public function test_admin_can_view_single_school()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('school-photo2.jpg');

        // Prepare the school data with a fake file
        $schoolData = [
            'title' => 'Test School',
            'address' => '123 Test St',
            'description' => 'This is a test school',
            'phone_number' => '555-1234',
            'website' => 'http://testschool.com',
            'founding_year' => 2000,
            'student_capacity' => 500,
            'photo' => $file, // Pass the fake file as the photo
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'school',
        ];


        // Create the school with the associated user
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            $schoolData
        );
        // Assert the request was successful and get the created school ID
        $response->assertStatus(201);
        $school = $response->json('data'); // Assuming 'data' key contains the school details
        $schoolId = $school['id'];

        // Retrieve the same school
        $response = $this->actingAs($admin)->getJson('/api/schools/' . $schoolId);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $school['id'],
                    'title' => $school['title'],
                    'photo' => $school['photo'],
                    'address' => $school['address'],
                    'description' => $school['description'],
                    'phone_number' => $school['phone_number'],
                    'website' => $school['website'],
                    'founding_year' => $school['founding_year'],
                    'student_capacity' => $school['student_capacity'],
                    'created_at' => $school['created_at'],
                    'updated_at' => $school['updated_at'],
                    'user' => $school['user'] ? [
                        'id' => $school['user']['id'],
                        'name' => $school['user']['name'],
                        'email' => $school['user']['email'],
                        'role' => $school['user']['role'],
                        'created_at' => $school['user']['created_at'],
                        'updated_at' => $school['user']['updated_at'],
                    ] : null,
                ]
            ]);
    }



    public function test_non_admin_cannot_view_schools_list()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        School::factory()->count(3)->create();

        $this->actingAs($user)->getJson('/api/schools')
            ->assertStatus(403);  // Unauthorized
    }


    public function test_admin_cannot_create_school_with_invalid_role_user()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Prepare the school data
        $schoolData = [
            'title' => 'Valid School',
            'address' => '456 Learning St.',
            'description' => 'A valid description.',
            'phone_number' => '555-1234',
            'founding_year' => 2000,
            'student_capacity' => 1000,

            'name' => 'Invalid User',
            'email' => 'invaliduser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin'  // Invalid role
        ];

        // Acting as an admin and attempting to create a school with a user of invalid role
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            $schoolData
        );

        // Assert that the request is forbidden (HTTP 422 Unprocessable Entity)
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        // Ensure that the school was not created in the database
        $this->assertDatabaseMissing('schools', ['title' => $schoolData['title']]);

        // Ensure that the user was not created in the database with the provided email
        $this->assertDatabaseMissing('users', ['email' => $schoolData['email']]);
    }


    public function test_non_admin_cannot_create_school()
    {
        // Create a user with a 'school' role, or any non-admin role
        $nonAdmin = User::factory()->create(['role' => 'school']);  // or 'teacher', 'student'

        // Prepare the school data
        $schoolData = [
            'title' => 'New School',
            'address' => '123 Education Lane',
            'description' => 'An outstanding school.',
            'phone_number' => '555-5555',
            'founding_year' => 1995,
            'student_capacity' => 800,

            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',  // Confirm password
            'role' => 'school'  // Ensure role is 'school' or non-admin
        ];

        // Acting as a non-admin user and attempting to create a school
        // The request should simulate separate school and user inputs
        $response = $this->actingAs($nonAdmin)->postJson(
            '/api/schools',
            $schoolData
        );

        // Assert that the request is forbidden (HTTP 403)
        $response->assertStatus(403);

        // Ensure that the school was not created in the database
        $this->assertDatabaseMissing('schools', ['title' => $schoolData['title']]);

        // Ensure that the user was not created in the database with the provided email
        $this->assertDatabaseMissing('users', ['email' => $schoolData['email']]);
    }


    public function test_can_create_school_with_photo_and_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('school-photo2.jpg');

        // Prepare the data
        $schoolData = [
            'title' => 'Test School',
            'address' => '123 Test St',
            'description' => 'This is a test school',
            'phone_number' => '555-1234',
            'website' => 'http://testschool.com',
            'founding_year' => 2000,
            'student_capacity' => 500,
            'photo' => $file, // Pass the fake file as the photo

            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',  // Confirm password
            'role' => 'school'  // Ensure role is 'school'
        ];

        // The request should simulate creating a school with the provided data
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            $schoolData
        );

        // Assert the request was successful
        $response->assertStatus(201);

        // Assert the file was stored in the correct directory
        Storage::disk('public')->assertExists('photos/' . $file->hashName());

        // Assert the school was created in the database
        $this->assertDatabaseHas('schools', [
            'title' => 'Test School',
            'photo' => 'photos/' . $file->hashName(), // Assert the photo path was saved
        ]);

        // Assert the associated user was created
        $this->assertDatabaseHas('users', [
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'role' => 'school'
        ]);
    }


    public function test_can_update_school_with_photo()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a school to update
        $school = School::factory()->create();

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('new-school-photo.jpg');

        // Prepare the updated data
        $updateData = [
            'title' => 'Updated School Title',
            'address' => '456 Updated St',
            'description' => 'This is an updated test school',
            'phone_number' => '555-5678',
            'website' => 'http://updatedschool.com',
            'founding_year' => 2010,
            'student_capacity' => 600,
            'photo' => $file, // Pass the fake file as the photo
        ];

        // Simulate the update request
        $response = $this->actingAs($admin)->putJson(
            '/api/schools/' . $school->id,
            $updateData
        );

        // Assert the request was successful
        $response->assertStatus(200);

        // Assert the old file was removed
        Storage::disk('public')->assertMissing('photos/old-photo.jpg');

        // Assert the new file was stored in the correct directory
        Storage::disk('public')->assertExists('photos/' . $file->hashName());

        // Assert the school was updated in the database
        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'title' => 'Updated School Title',
            'photo' => 'photos/' . $file->hashName(), // Assert the new photo path was saved
        ]);
    }


    public function test_school_and_associated_user_are_deleted()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Fake the storage disk for testing purposes
        Storage::fake('public');

        // Create a fake file for testing
        $file = UploadedFile::fake()->image('school-photo2.jpg');

        // Prepare the data
        $schoolData = [
            'title' => 'Test School',
            'address' => '123 Test St',
            'description' => 'This is a test school',
            'phone_number' => '555-1234',
            'website' => 'http://testschool.com',
            'founding_year' => 2000,
            'student_capacity' => 500,
            'photo' => $file, // Pass the fake file as the photo

            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',  // Confirm password
            'role' => 'school'  // Ensure role is 'school'
        ];

        // The request should simulate creating a school with the provided data
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            $schoolData
        );
        // Decode the response data
        $responseData = $response->json();

        // Access the school ID
        $schoolId = $responseData['data']['id'];

        $userId = $responseData['data']['user']['id'];


        // Perform the delete request
        $response = $this->actingAs($admin)->deleteJson('/api/schools/' . $schoolId);

        // Assert the response status is 204 No Content
        $response->assertStatus(204);

        // Assert the school no longer exists in the database
        $this->assertDatabaseMissing('schools', ['id' => $schoolId]);

        // Assert the associated user no longer exists in the database
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }


    public function test_attach_students_to_school()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a school and students
        $school = School::factory()->create();
        $students = Student::factory()->count(3)->create();

        // Prepare the request data
        $data = [
            'student_ids' => $students->pluck('id')->toArray()
        ];

        // Make the POST request to attach students to the school
        $response = $this->actingAs($admin)->postJson("/api/schools/{$school->id}/students", $data);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['message' => 'Students attached to the school successfully.'])
            ->assertJsonStructure(['attached_student_count']);

        // Check that the students were attached to the school
        foreach ($students as $student) {
            $this->assertDatabaseHas('school_student', [
                'school_id' => $school->id,
                'student_id' => $student->id,
            ]);
        }
    }

    public function test_attach_teachers_to_school()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a school and teachers
        $school = School::factory()->create();
        $teachers = Teacher::factory()->count(3)->create();

        // Prepare the request data
        $data = [
            'teacher_ids' => $teachers->pluck('id')->toArray()
        ];

        // Make the POST request to attach teachers to the school
        $response = $this->actingAs($admin)->postJson("/api/schools/{$school->id}/teachers", $data);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['message' => 'Teachers attached to the school successfully.'])
            ->assertJsonStructure(['attached_teacher_count']);

        // Check that the teachers were attached to the school
        foreach ($teachers as $teacher) {
            $this->assertDatabaseHas('school_teacher', [
                'school_id' => $school->id,
                'teacher_id' => $teacher->id,
            ]);
        }

    }


    public function test_list_students_in_school()
    {
        // Create an admin user and authenticate
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a school and some students
        $school = School::factory()->create();
        $students = Student::factory()->count(3)->create();

        // Attach students to the school
        $school->students()->attach($students->pluck('id')->toArray());

        // Make the GET request
        $response = $this->actingAs($admin)->getJson("/api/schools/{$school->id}/students");

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'photo'], // Adjust according to your Student resource structure
                ],
                'links' => [
                    'self',
                ],
            ]);

        // Assert that the students are included in the response
        foreach ($students as $student) {
            $this->assertDatabaseHas('students', [
                'id' => $student->id,
            ]);
        }
    }

    public function test_list_teachers_in_school()
    {
        // Create an admin user and authenticate
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a school and some teachers
        $school = School::factory()->create();
        $teachers = Teacher::factory()->count(3)->create();

        // Attach teachers to the school
        $school->teachers()->attach($teachers->pluck('id')->toArray());

        // Make the GET request
        $response = $this->actingAs($admin)->getJson("/api/schools/{$school->id}/teachers");

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'photo', 'phone_number', 'bio'], // Adjust according to your Teacher resource structure
                ],
                'links' => [
                    'self',
                ],
            ]);

        // Assert that the teachers are included in the response
        foreach ($teachers as $teacher) {
            $this->assertDatabaseHas('teachers', [
                'id' => $teacher->id,
            ]);
        }
    }

}
