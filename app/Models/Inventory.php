<?php

namespace App\Models;

use Database\Factories\InventoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    /** @use HasFactory<InventoryFactory> */
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
    ];

    protected function casts(): array{
        return [
            'quantity' => 'integer',
        ];
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo{
        return $this->belongsTo(Item::class);
    }
}

