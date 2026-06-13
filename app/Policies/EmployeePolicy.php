<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view employees');
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->can('view employees');
    }

    public function create(User $user): bool
    {
        return $user->can('create employees');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can('edit employees');
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('delete employees');
    }
}