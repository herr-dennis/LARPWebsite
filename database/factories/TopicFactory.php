<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rubric;

class TopicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'topic_name' => $this->faker->sentence(3),
            'topic_verfasser' => $this->faker->name(),
            'rubric_id' => Rubric::factory(),
            'topic_status' => $this->faker->boolean(),
        ];
    }
}
