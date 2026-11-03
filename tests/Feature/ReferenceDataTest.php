<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    // ------------------ Database Structure ------------------

    public function test_domain_tables_can_be_created_from_migrations(): void
    {
        $this->assertTrue(Schema::hasTable('items'));
        $this->assertTrue(Schema::hasTable('inventory'));

        $this->assertTrue(Schema::hasColumns('items', [
            'name',
            'price',
            'effect_type',
            'duration_minutes',
        ]));
    }

    public function test_purchases_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('purchases'));

        $this->assertTrue(Schema::hasColumns('purchases', [
            'user_id',
            'item_id',
            'quantity',
            'unit_price',
            'total',
            'created_at',
        ]));
    }

    // ------------------ Users ------------------

    public function test_new_users_start_with_zero_coins(): void
    {
        $user = User::factory()->create()->refresh();

        $this->assertSame(0, $user->coins);
    }

    // ------------------ Items ------------------

    public function test_items_accept_an_integer_price_and_a_null_duration(): void
    {
        $item = Item::factory()->create([
            'price' => 72,
            'duration_minutes' => null,
        ])->refresh();

        $this->assertSame(72, $item->price);
        $this->assertNull($item->duration_minutes);
    }

    // ------------------ Inventory ------------------

    public function test_inventory_relations_work_in_both_directions(): void
    {
        $stock = Inventory::factory()->create()->refresh();

        $user = $stock->user;
        $item = $stock->item;

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Item::class, $item);

        $this->assertTrue(
            $user->inventory->contains('id', $stock->id)
        );

        $this->assertTrue(
            $item->inventory->contains('id', $stock->id)
        );

        $this->assertIsInt($stock->quantity);
    }

    public function test_inventory_prevents_duplicate_user_item_pairs(): void
    {
        $stock = Inventory::factory()->create();

        $this->expectException(QueryException::class);

        Inventory::create([
            'user_id' => $stock->user_id,
            'item_id' => $stock->item_id,
            'quantity' => 2,
        ]);
    }

    public function test_different_users_can_have_the_same_item(): void
    {
        $item = Item::factory()->create();

        Inventory::factory()->create([
            'item_id' => $item->id,
        ]);

        Inventory::factory()->create([
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseCount('inventory', 2);
    }

    public function test_deleting_a_user_removes_their_inventory(): void
    {
        $stock = Inventory::factory()->create();

        $stock->user->delete();

        $this->assertDatabaseMissing('inventory', [
            'id' => $stock->id,
        ]);
    }

    public function test_an_item_in_inventory_cannot_be_deleted(): void
    {
        $stock = Inventory::factory()->create();

        $this->expectException(QueryException::class);

        $stock->item->delete();
    }

    // ------------------ Purchases ------------------

    public function test_purchase_relations_work(): void
    {
        $purchase = Purchase::factory()->create();

        $this->assertInstanceOf(User::class, $purchase->user);
        $this->assertInstanceOf(Item::class, $purchase->item);

        $this->assertTrue(
            $purchase->user->purchases->contains('id', $purchase->id)
        );

        $this->assertTrue(
            $purchase->item->purchases->contains('id', $purchase->id)
        );
    }

    public function test_purchase_keeps_historical_price(): void
    {
        $item = Item::factory()->create(['price' => 50]);

        $purchase = Purchase::factory()->create([
            'item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 50,
            'total' => 100,
        ]);

        $item->update(['price' => 80]);

        $purchase->refresh();

        $this->assertSame(50, $purchase->unit_price);
        $this->assertSame(100, $purchase->total);
    }


}
