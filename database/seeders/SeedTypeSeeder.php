<?php

namespace Database\Seeders;

use App\Models\SeedType;
use Illuminate\Database\Seeder;

class SeedTypeSeeder extends Seeder
{
    public function run(): void
    {
        $seedTypes = [
            [
                'name' => 'Roble',
                'description' => 'Árbol de crecimiento resistente.',
                'cares_per_level' => 5,
                'harvest_reward' => 50,
            ],
            [
                'name' => 'Cerezo',
                'description' => 'Árbol ornamental de flores coloridas.',
                'cares_per_level' => 5,
                'harvest_reward' => 75,
            ],
            [
                'name' => 'Pino',
                'description' => 'Árbol de hojas perennes.',
                'cares_per_level' => 5,
                'harvest_reward' => 40,
            ],
            [
                'name' => 'Mango',
                'description' => 'Árbol frutal de clima tropical.',
                'cares_per_level' => 5,
                'harvest_reward' => 100,
            ],
            [
                'name' => 'Aguacate',
                'description' => 'Árbol frutal de hojas verdes.',
                'cares_per_level' => 5,
                'harvest_reward' => 90,
            ],
            [
                'name' => 'Guayaba',
                'description' => 'Árbol frutal de clima tropical.',
                'cares_per_level' => 5,
                'harvest_reward' => 60,
            ],
        ];

        foreach ($seedTypes as $seedType) {
            SeedType::updateOrCreate(
                ['name' => $seedType['name']],
                $seedType
            );
        }
    }
}
