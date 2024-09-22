<?php
namespace Tests\Feature\Teacher;

use App\Models\teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class TeacherSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_teachers_returns_paginated_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Arrange: Create teachers
        Teacher::factory()->count(5)->create(['name' => 'John Doe']);
        Teacher::factory()->count(5)->create(['name' => 'Jane Doe']);

        // Act: Search for "John"
        $response = $this->actingAs($admin)->getJson(route('teachers.search', ['search' => 'John']));

        // Assert: Ensure 5 results are returned
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['name' => 'John Doe']);
    }

    public function test_search_teachers_requires_search_term()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Act: Search without providing search term
        $response = $this->actingAs($admin)->getJson(route('teachers.search'));

        // Assert: Validation error for missing search term
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }
}
