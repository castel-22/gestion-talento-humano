<?php

namespace App\Policies;

use App\Models\GuardRotation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardRotationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GuardRotation $guardRotation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    public function update(User $user, GuardRotation $guardRotation): bool
    {
        return $user->hasRole('administrador') || $user->hasRole('supervisor');
    }

    public function delete(User $user, GuardRotation $guardRotation): bool
    {
        return $user->hasRole('administrador');
    }
}