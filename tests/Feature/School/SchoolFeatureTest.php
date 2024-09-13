<?php

namespace Tests\Feature\School;

use App\Models\School;
use App\Models\User;
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
        School::factory()->count(3)->create();

        $this->actingAs($admin)->getJson('/api/schools')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'address', 'description', 'phone_number', 'website', 'founding_year', 'student_capacity', 'created_at', 'updated_at']
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
        ];

        $userData = [
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'school',
        ];


        // Create the school with the associated user
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            array_merge($schoolData, $userData)
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
        ];

        // Prepare the user data with an invalid role (e.g., 'admin')
        $userData = [
            'name' => 'Invalid User',
            'email' => 'invaliduser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin'  // Invalid role
        ];

        // Acting as an admin and attempting to create a school with a user of invalid role
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            array_merge($schoolData, $userData)
        );

        // Assert that the request is forbidden (HTTP 422 Unprocessable Entity)
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        // Ensure that the school was not created in the database
        $this->assertDatabaseMissing('schools', ['title' => $schoolData['title']]);

        // Ensure that the user was not created in the database with the provided email
        $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
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
        ];

        // Prepare the user data for registration
        $userData = [
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
            array_merge($schoolData, $userData)
        );

        // Assert that the request is forbidden (HTTP 403)
        $response->assertStatus(403);

        // Ensure that the school was not created in the database
        $this->assertDatabaseMissing('schools', ['title' => $schoolData['title']]);

        // Ensure that the user was not created in the database with the provided email
        $this->assertDatabaseMissing('users', ['email' => $userData['email']]);
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
        ];

        $userData = [
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',  // Confirm password
            'role' => 'school'  // Ensure role is 'school'
        ];

        // The request should simulate creating a school with the provided data
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            array_merge($schoolData, $userData)
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
        ];

        $userData = [
            'name' => 'School User',
            'email' => 'schooluser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',  // Confirm password
            'role' => 'school'  // Ensure role is 'school'
        ];

        // The request should simulate creating a school with the provided data
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            array_merge($schoolData, $userData)
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
}
