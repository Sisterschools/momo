<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->company,
            'photo' => $this->faker->imageUrl(640, 480, 'schools', true),
            'address' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'phone_number' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'founding_year' => $this->faker->year,
            'student_capacity' => $this->faker->numberBetween(100, 5000),
        ];
    }
}
