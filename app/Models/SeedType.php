<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cares_per_level',
        'harvest_reward',
    ];

    protected function casts(): array
    {
        return [
            'cares_per_level' => 'integer',
            'harvest_reward' => 'integer',
        ];
    }

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }
}
