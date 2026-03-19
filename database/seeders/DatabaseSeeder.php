<?php

namespace Database\Seeders;

use App\Models\DonnerfelsStory;
use App\Models\Pionier;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rubric;
use App\Models\Topic;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        /*
        Rubric::factory()
            ->count(10)
            ->has(
                Topic::factory()
                    ->count(10)
                    ->has(
                        Posts::factory()->count(10)
                    )
            )
            ->create();


        DonnerfelsStory::factory()->count(10)->create();

      */

        Pionier::factory()->count(10)->create();

    }
}
