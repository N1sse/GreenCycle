<?php

namespace Database\Factories;

use App\Models\ActiveEffect;
use App\Models\Item;
use App\Models\Tree;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActiveEffect>
 */
class ActiveEffectFactory extends Factory
{
    protected $model = ActiveEffect::class;

    public function definition(): array
    {
        $startedAt = now();

        return [
            'tree_id' => Tree::factory(),
            'item_id' => Item::factory(),
            'started_at' => $startedAt,
            'expires_at' => $startedAt->copy()->addMinutes(30),
        ];
    }
}
