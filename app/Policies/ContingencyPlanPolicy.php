<?php

namespace App\Policies;

use App\Models\ContingencyPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContingencyPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ContingencyPlan $contingencyPlan): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('administrador') || $user->hasRole('supervisor');
    }

    public function update(User $user, ContingencyPlan $contingencyPlan): bool
    {
        return $user->hasRole('administrador') || $user->hasRole('supervisor');
    }

    public function delete(User $user, ContingencyPlan $contingencyPlan): bool
    {
        return $user->hasRole('administrador');
    }
}