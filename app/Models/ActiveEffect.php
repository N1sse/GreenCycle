<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveEffect extends Model
{
    use HasFactory;

    protected $fillable = [
        'tree_id',
        'item_id',
        'started_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // ------------------ Tree ------------------
    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }

    // ------------------ Item ------------------
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
