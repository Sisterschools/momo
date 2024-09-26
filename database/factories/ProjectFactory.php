<?php
namespace Database\Factories;

use App\Models\Project;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'school_id_1' => School::factory(),
            'school_id_2' => School::factory(),
        ];
    }
}
