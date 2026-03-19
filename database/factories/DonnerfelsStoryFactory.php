<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DonnerfelsStoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->optional()->sentence(3), // manchmal null
            'text' => $this->faker->paragraphs(3, true),
            'image' => $this->faker->optional()->imageUrl(800, 600, 'nature'),
        ];
    }
}
