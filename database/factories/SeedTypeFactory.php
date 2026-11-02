<?php

namespace Database\Factories;

use App\Models\SeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SeedType>
 */
class SeedTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
            'cares_per_level' => 5,
            'harvest_reward' => fake()->numberBetween(10, 100),
        ];
    }
}
