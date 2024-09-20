<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'photo' => null,  // You can set this when testing file uploads
            'phone_number' => $this->faker->phoneNumber,
            'bio' => $this->faker->paragraph,
        ];
    }
}
