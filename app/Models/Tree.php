<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seed_type_id',
        'level',
        'progress',
        'status',
        'health',
        'planted_at',
        'last_cared_at',
        'next_care_at',
        'last_decay_at',
        'harvested_at',
    ];

    protected function casts(): array
    {
        return [
            'planted_at' => 'datetime',
            'last_cared_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'harvested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seedType(): BelongsTo
    {
        return $this->belongsTo(SeedType::class);
    }

    public function activeEffects(): HasMany
    {
        return $this->hasMany(ActiveEffect::class);
    }
}
