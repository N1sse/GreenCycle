<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'seed_type_id' => $this->seed_type_id,
            'level' => $this->level,
            'progress' => $this->progress,
            'status' => $this->status,
            'health' => $this->health,
            'planted_at' => $this->planted_at,
            'last_cared_at' => $this->last_cared_at,
            'next_care_at' => $this->next_care_at,
            'last_decay_at' => $this->last_decay_at,
            'harvested_at' => $this->harvested_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
