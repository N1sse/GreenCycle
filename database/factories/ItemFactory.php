<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'price' => fake()->numberBetween(10, 100),
            'effect_type' => 'example',
            'duration_minutes' => fake()->randomElement([
                null,
                30,
                60,
            ]),
        ];
    }
}
