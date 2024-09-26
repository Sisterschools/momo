<?php
namespace Tests\Feature\School;

use App\Models\school;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SearchSchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_schools_returns_paginated_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Arrange: Create schools
        School::factory()->count(5)->create(['title' => 'School1']);
        School::factory()->count(5)->create(['title' => 'School2']);

        // Act: Search for "John"
        $response = $this->actingAs($admin)->getJson(route('schools.search', ['search' => 'School1']));

        // Assert: Ensure 5 results are returned
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['title' => 'School1']);
    }

    public function test_search_schools_requires_search_term()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Act: Search without providing search term
        $response = $this->actingAs($admin)->getJson(route('schools.search'));

        // Assert: Validation error for missing search term
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }
}
