<?php

namespace Database\Factories;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tree>
 */
class TreeFactory extends Factory
{
    protected $model = Tree::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'seed_type_id' => 1,
            'level' => 0,
            'progress' => 0,
            'status' => 'ACTIVE',
            'health' => 100,
            'planted_at' => now(),
            'last_cared_at' => null,
            'next_care_at' => null,
            'last_decay_at' => null,
            'harvested_at' => null,
        ];
    }
}
