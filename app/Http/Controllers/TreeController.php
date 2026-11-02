<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use App\Http\Resources\TreeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TreeController extends Controller
{
    // Listar los árboles del usuario autenticado.
    public function index(Request $request)
    {
        $trees = Tree::where('user_id', $request->user()->id)
            ->with('seedType')
            ->get();

        return TreeResource::collection($trees);
    }

    // Mostrar un árbol específico.
    public function show(Tree $tree)
    {
        Gate::authorize('view', $tree);

        return new TreeResource($tree->load('seedType'));
    }

    // Crear un árbol.
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'seed_type_id' => ['required', 'exists:seed_types,id'],
        ]);

        $tree = Tree::create([
            'user_id' => $request->user()->id,
            'seed_type_id' => $validated['seed_type_id'],
            'level' => 0,
            'progress' => 0,
            'status' => 'ACTIVE',
            'health' => 100,
            'planted_at' => now(),
            'last_cared_at' => null,
            'next_care_at' => null,
            'last_decay_at' => null,
            'harvested_at' => null,
        ]);

        return response()->json([
            'message' => '¡Árbol plantado correctamente!',
            'tree' => new TreeResource($tree),
        ], 201);
    }

    // Eliminar un árbol.
    public function destroy(Tree $tree): JsonResponse
    {
        Gate::authorize('delete', $tree);

        $tree->delete();

        return response()->json([
            'message' => 'Árbol eliminado correctamente.',
        ], 200);
    }
}
