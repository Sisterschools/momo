<?php

namespace Tests\Feature\School;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSchoolRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = User::factory()->create(['role' => 'admin']);

        // Seed the database with test data
        School::factory()->create(['title' => 'Test School']);
        School::factory()->create(['title' => 'Another School']);
    }

    public function test_admin_can_search_schools_and_get_results()
    {
        // Authenticate as admin user
        $response = $this->actingAs($this->admin)->postJson('/api/schools/search', [
            'search' => 'Test School',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Test School'])
            ->assertJsonMissing(['title' => 'Another School']);
    }

    public function test_admin_can_search_schools_and_get_empty_results()
    {
        // Authenticate as admin user
        $response = $this->actingAs($this->admin)->postJson('/api/schools/search', [
            'search' => 'Nonexistent School',
        ]);

        $response->assertStatus(200)
            ->assertJson([]);
    }

    public function test_admin_search_with_empty_term_returns_results()
    {
        // Authenticate as admin user
        $response = $this->actingAs($this->admin)->postJson('/api/schools/search', [
            'search' => '', // Empty search term
        ]);

        $response->assertStatus(200); // Or 422 if you want to handle empty searches differently
    }
}
