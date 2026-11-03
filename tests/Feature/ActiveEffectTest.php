<?php

namespace Tests\Feature;

use App\Models\ActiveEffect;
use App\Models\Item;
use App\Models\Tree;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActiveEffectTest extends TestCase
{
    use RefreshDatabase;

    // ------------------ Migrations ------------------
    public function test_active_effects_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('active_effects'));

        $this->assertTrue(Schema::hasColumns('active_effects', [
            'tree_id',
            'item_id',
            'started_at',
            'expires_at',
        ]));
    }

    // ------------------ Relationships ------------------
    public function test_active_effect_relations_work(): void
    {
        $effect = ActiveEffect::factory()->create();

        $this->assertInstanceOf(Tree::class, $effect->tree);
        $this->assertInstanceOf(Item::class, $effect->item);

        $this->assertTrue(
            $effect->tree->activeEffects->contains('id', $effect->id)
        );

        $this->assertTrue(
            $effect->item->activeEffects->contains('id', $effect->id)
        );
    }

    // ------------------ Duration ------------------
    public function test_active_effect_has_valid_dates(): void
    {
        $effect = ActiveEffect::factory()->create();

        $this->assertTrue(
            $effect->expires_at->greaterThan($effect->started_at)
        );
    }

    // ------------------ Delete ------------------
    public function test_deleting_tree_removes_its_effects(): void
    {
        $effect = ActiveEffect::factory()->create();

        $effect->tree->delete();

        $this->assertDatabaseMissing('active_effects', [
            'id' => $effect->id,
        ]);
    }
}
