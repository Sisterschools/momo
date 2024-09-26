<?php
namespace Tests\Feature\Program;

use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProgramSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_programs_returns_paginated_results()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // Arrange: Create programs
        Program::factory()->count(5)->create(['name' => 'Math Program']);
        Program::factory()->count(5)->create(['name' => 'Science Program']);
        // Act: Search for "Math"
        $response = $this->actingAs($admin)->getJson(route('programs.search', ['search' => 'Math']));

        // Assert: Ensure 5 results are returned
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment(['name' => 'Math Program']);
    }

    public function test_search_programs_requires_search_term()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // Act: Search without providing search term
        $response = $this->actingAs($admin)->getJson(route('programs.search'));

        // Assert: Validation error for missing search term
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }
}
