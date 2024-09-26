<?php
namespace Tests\Feature\Student;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class StudentSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_students_returns_paginated_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Arrange: Create students
        Student::factory()->count(5)->create(['name' => 'John Doe']);
        Student::factory()->count(5)->create(['name' => 'Jane Doe']);

        // Act: Search for "John"
        $response = $this->actingAs($admin)->getJson(route('students.search', ['search' => 'John']));

        // Assert: Ensure 5 results are returned
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['name' => 'John Doe']);
    }

    public function test_search_students_requires_search_term()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Act: Search without providing search term
        $response = $this->actingAs($admin)->getJson(route('students.search'));

        // Assert: Validation error for missing search term
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }
}
