<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view departments');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can('view departments');
    }

    public function create(User $user): bool
    {
        return $user->can('create departments');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('edit departments');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->can('delete departments');
    }
}