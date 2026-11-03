<?php

namespace App\Policies;

use App\Models\Tree;
use App\Models\User;

class TreePolicy
{
    // Permitir consultar la lista de árboles.
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Solo el propietario puede ver su árbol.
    public function view(User $user, Tree $tree): bool
    {
        return $user->id === $tree->user_id;
    }

    // Un usuario autenticado puede crear árboles.
    public function create(User $user): bool
    {
        return true;
    }

    // Solo el propietario puede modificar su árbol.
    public function update(User $user, Tree $tree): bool
    {
        return $user->id === $tree->user_id;
    }

    // Solo el propietario puede eliminar su árbol.
    public function delete(User $user, Tree $tree): bool
    {
        return $user->id === $tree->user_id;
    }

    // Solo el propietario puede cuidar su árbol.
    public function care(User $user, Tree $tree): bool
    {
        return $user->id === $tree->user_id;
    }
}
