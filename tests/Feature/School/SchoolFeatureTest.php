<?php

namespace Tests\Feature\School;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

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

    public function test_non_admin_cannot_view_schools_list()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        School::factory()->count(3)->create();

        $this->actingAs($user)->getJson('/api/schools')
            ->assertStatus(403);  // Unauthorized
    }


    public function test_admin_can_create_school_with_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $schoolData = [
            'title' => 'Test School',
            'address' => '123 School St.',
            'description' => 'A test description',
            'phone_number' => '123-456-7890',
            'founding_year' => 2000,
            'student_capacity' => 500,
        ];

        $userData = [
            'name' => 'School Admin',
            'email' => 'school@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'school'
        ];

        // The request should simulate separate school and user inputs
        $response = $this->actingAs($admin)->postJson(
            '/api/schools',
            array_merge($schoolData, $userData)
        );

        // $response = $this->postJson('/api/schools', [
        //     'school' => $schoolData,
        //     'user' => $userData
        // ]);


        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => $schoolData['title'],
                    'address' => $schoolData['address'],
                    'description' => $schoolData['description'],
                ]
            ]);

        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
        $this->assertTrue(Hash::check($userData['password'], User::first()->password));
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

}
