<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PionierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),

            'waffen' => $this->faker->randomElement([
                'Schwert',
                'Axt',
                'Speer',
                'Bogen',
                'Armbrust',
                'Hammer'
            ]),

            'text' => $this->faker->paragraph(3),

            'image' => $this->faker->imageUrl(400, 400, 'people', true),

            'rang' => $this->faker->randomElement([
                'Rekrut',
                'Soldat',
                'Veteran',
                'Hauptmann',
                'Kommandant'
            ]),

            'dienstjahre' => $this->faker->numberBetween(1, 30) . ' Jahre',

            'geburtstag' => $this->faker->date('d.m.Y'),
        ];
    }
}
