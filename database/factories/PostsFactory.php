<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_author' => $this->faker->name(),
            'post_text' => $this->faker->text(),
        ];
    }
}
