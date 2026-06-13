<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Leave;

class LeavePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'supervisor', 'secretaria']);
    }

    public function view(User $user, Leave $leave): bool
    {
        return $user->hasAnyRole(['administrador', 'supervisor', 'secretaria']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['administrador', 'supervisor', 'secretaria']);
    }

    public function update(User $user, Leave $leave): bool
    {
        return $user->hasAnyRole(['administrador', 'supervisor']);
    }

    public function delete(User $user, Leave $leave): bool
    {
        // Solo administrador puede eliminar (ajusta si quieres que supervisor también)
        return $user->hasRole('administrador');
    }
}