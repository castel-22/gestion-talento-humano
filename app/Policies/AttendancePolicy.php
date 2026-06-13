<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view attendances');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->can('view attendances');
    }

    public function create(User $user): bool
    {
        return $user->can('create attendances');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->can('edit attendances');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->can('delete attendances');
    }
}