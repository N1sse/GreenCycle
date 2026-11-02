<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tree;
use App\Models\SeedType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreeCareTest extends TestCase
{
    use RefreshDatabase;

    // 1. Comprobar que cuidar un árbol funciona correctamente.
    public function test_user_can_care_for_their_tree(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'health' => 70,
            'progress' => 0,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/trees/{$tree->id}/care");

        $response->assertStatus(200)
            ->assertJsonPath('tree.health', 90)
            ->assertJsonPath('tree.progress', 1);

        $tree->refresh();

        $this->assertNotNull($tree->last_cared_at);
        $this->assertNotNull($tree->next_care_at);
    }

    // 2. Impedir dos cuidados consecutivos.
    public function test_user_cannot_care_for_tree_twice_immediately(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'health' => 80,
            'progress' => 0,
        ]);

        $this->actingAs($user, 'sanctum');

        $this->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(200);

        $tree->refresh();

        $health = $tree->health;
        $progress = $tree->progress;

        $this->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(409);

        $tree->refresh();

        $this->assertEquals($health, $tree->health);
        $this->assertEquals($progress, $tree->progress);
    }

    // 3. Subir de nivel después de cinco cuidados.
    public function test_tree_levels_up_after_five_cares(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'level' => 0,
            'progress' => 0,
        ]);

        $this->actingAs($user, 'sanctum');

        for ($i = 0; $i < 5; $i++) {
            $this->postJson("/api/trees/{$tree->id}/care")
                ->assertStatus(200);

            if ($i < 4) {
                $this->travel(61)->minutes();
            }
        }

        $tree->refresh();

        $this->assertEquals(1, $tree->level);
        $this->assertEquals(0, $tree->progress);
    }

    // 4. El árbol madura al alcanzar el nivel 10.
    public function test_tree_becomes_mature_at_level_ten(): void
    {
        $user = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'level' => 9,
            'progress' => 4,
            'status' => 'ACTIVE',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(200);

        $tree->refresh();

        $this->assertEquals(10, $tree->level);
        $this->assertEquals(0, $tree->progress);
        $this->assertEquals('MATURE', $tree->status);
    }

    // 5. Impedir que un usuario cuide el árbol de otra persona.
    public function test_user_cannot_care_for_another_users_tree(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $tree = Tree::factory()->create([
            'user_id' => $owner->id,
            'health' => 70,
            'progress' => 0,
        ]);

        $this->actingAs($otherUser, 'sanctum')
            ->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(403);

        $tree->refresh();

        $this->assertEquals(70, $tree->health);
        $this->assertEquals(0, $tree->progress);
    }

    public function test_guest_cannot_care_for_a_tree(): void
{
    $tree = Tree::factory()->create();

    $this->postJson("/api/trees/{$tree->id}/care")
        ->assertStatus(401);
}

//árbol no existe
public function test_cannot_care_for_a_nonexistent_tree(): void
{
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/trees/99999/care')
        ->assertStatus(404);
}

//árboles que ya no pueden recibir cuidados
public function test_cannot_care_for_trees_that_are_not_active(): void
{
    $user = User::factory()->create();

    foreach (['DEAD', 'MATURE', 'HARVESTED'] as $status) {
        $tree = Tree::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/trees/{$tree->id}/care")
            ->assertStatus(409);
    }
}

//la salud no puede superar 100
public function test_tree_health_cannot_exceed_100(): void
{
    $user = User::factory()->create();

    $tree = Tree::factory()->create([
        'user_id' => $user->id,
        'health' => 95,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/trees/{$tree->id}/care")
        ->assertStatus(200)
        ->assertJsonPath('tree.health', 100);

    $tree->refresh();

    $this->assertEquals(100, $tree->health);
}

//límite exacto de los 60 minuto
public function test_tree_can_be_cared_for_exactly_at_next_care_time(): void
{
    $user = User::factory()->create();

    $tree = Tree::factory()->create([
        'user_id' => $user->id,
        'health' => 70,
        'next_care_at' => now()->addMinutes(60),
    ]);

    $this->travel(60)->minutes();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/trees/{$tree->id}/care")
        ->assertStatus(200);

    $tree->refresh();

    $this->assertEquals(90, $tree->health);
    $this->assertEquals(1, $tree->progress);
}
}
