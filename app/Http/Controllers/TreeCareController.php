<?php

namespace App\Http\Controllers;

use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TreeCareController extends Controller
{
    public function care(Tree $tree): JsonResponse
    {
        return DB::transaction(function () use ($tree) {

            // Obtener el árbol y bloquearlo durante la operación.
            $tree = Tree::whereKey($tree->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Verificar que el árbol pertenezca al usuario.
            Gate::authorize('care', $tree);

            // Comprobar si el árbol ya terminó su ciclo.
            if (in_array($tree->status, ['DEAD', 'MATURE', 'HARVESTED'])) {
                return response()->json([
                    'message' => 'Este árbol ya no puede recibir cuidados.',
                    'status' => $tree->status,
                ], 409);
            }

            $now = now();

            // Comprobar el tiempo de espera.
            if ($tree->next_care_at !== null &&
                $now->lt($tree->next_care_at)) {

                return response()->json([
                    'message' => 'Todavía no puedes cuidar este árbol.',
                    'next_care_at' => $tree->next_care_at,
                ], 409);
            }

            // Recuperar 20 puntos de salud, máximo 100.
            $tree->health = min(100, $tree->health + 20);

            // Sumar un punto de progreso.
            $tree->progress++;

            // Cada cinco cuidados aumenta un nivel.
            if ($tree->progress >= 5) {
                $tree->level++;
                $tree->progress = 0;
            }

            // Al alcanzar el nivel 10, el árbol madura.
            if ($tree->level >= 10) {
                $tree->status = 'MATURE';
            }

            // Registrar las fechas del cuidado.
            $tree->last_cared_at = $now;
            $tree->next_care_at = $now->copy()->addMinutes(60);

            $tree->save();

            return response()->json([
                'message' => '¡Árbol cuidado correctamente!',
                'tree' => new TreeResource($tree),
            ], 200);
        });
    }
}
