<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProgramFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $program;
    protected $projects;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a program and associated projects
        $this->program = Program::factory()->create(['name' => 'Math Program']);
        $this->projects = Project::factory()->count(3)->create();

        // Attach the projects to the program
        $this->program->projects()->sync($this->projects->pluck('id'));
        $this->admin = User::factory()->create(['role' => 'admin']);

    }

    public function test_get_all_projects_for_program()
    {
        $response = $this->actingAs($this->admin)->getJson(route('programs.projects', $this->program));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // Assuming you're using a resource collection
    }

    public function test_create_program()
    {
        $response = $this->actingAs($this->admin)->postJson(route('programs.store'), [
            'name' => 'Science Program',
            'description' => 'A program for science studies',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Science Program']);

        $this->assertDatabaseHas('programs', ['name' => 'Science Program']);
    }

    public function test_update_program()
    {
        $response = $this->actingAs($this->admin)->patchJson(route('programs.update', $this->program), [
            'name' => 'Updated Math Program',
            'description' => 'An updated program for math studies',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Math Program']);

        $this->assertDatabaseHas('programs', ['name' => 'Updated Math Program']);
    }

    public function test_delete_program()
    {
        $response = $this->actingAs($this->admin)->deleteJson(route('programs.destroy', $this->program));

        $response->assertStatus(204);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('programs', ['id' => $this->program->id]);
    }
}
