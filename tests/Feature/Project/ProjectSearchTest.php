<?php
namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProjectSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_projects_returns_paginated_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Arrange: Create projects
        Project::factory()->count(5)->create(['name' => 'AI Research']);
        Project::factory()->count(5)->create(['name' => 'Web Development']);

        // Act: Search for "AI"
        $response = $this->actingAs($admin)->getJson(route('projects.search', ['search' => 'AI']));

        // Assert: Ensure 5 results are returned
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['name' => 'AI Research']);
    }

    public function test_search_projects_requires_search_term()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Act: Search without providing search term
        $response = $this->actingAs($admin)->getJson(route('projects.search'));

        // Assert: Validation error for missing search term
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }
}
