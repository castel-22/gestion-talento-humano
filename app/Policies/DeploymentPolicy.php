<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Deployment;

class DeploymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view deployments');
    }

    public function view(User $user, Deployment $deployment): bool
    {
        return $user->can('view deployments');
    }

    public function create(User $user): bool
    {
        return $user->can('create deployments');
    }

    public function update(User $user, Deployment $deployment): bool
    {
        return $user->can('edit deployments');
    }

    public function delete(User $user, Deployment $deployment): bool
    {
        return $user->can('delete deployments');
    }
}