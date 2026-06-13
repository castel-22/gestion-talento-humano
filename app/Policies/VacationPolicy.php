<?php

namespace App\Policies;

use App\Models\Vacation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class VacationPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier vacación.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver una vacación específica.
     */
    public function view(User $user, Vacation $vacation): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede crear una vacación.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('administrador') || $user->hasRole('supervisor') || $user->hasRole('secretaria');
    }

    /**
     * Determina si el usuario puede actualizar una vacación.
     * Si no se pasa modelo (acciones masivas), se permite si es admin/supervisor.
     */
    public function update(User $user, ?Vacation $vacation = null): bool
    {
        if (is_null($vacation)) {
            // Autorización genérica (acciones masivas)
            return $user->hasRole('administrador') || $user->hasRole('supervisor');
        }

        // Si el modelo está presente, se puede aplicar lógica adicional (ej. solo el creador)
        return $user->hasRole('administrador') || $user->hasRole('supervisor');
    }

    /**
     * Determina si el usuario puede eliminar una vacación.
     */
    public function delete(User $user, Vacation $vacation): bool
    {
        return $user->hasRole('administrador');
    }
}