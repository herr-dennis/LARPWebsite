<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RubricFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rubric_verfasser' => $this->faker->name(),
            'rubric_name' => $this->faker->words(2, true),
            'rubric_status' => $this->faker->boolean()
        ];
    }
}
