<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Backup;

class BackupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view backups');
    }

    public function view(User $user, Backup $backup): bool
    {
        return $user->can('view backups');
    }

    public function create(User $user): bool
    {
        return $user->can('create backups');
    }

    public function update(User $user, Backup $backup): bool
    {
        // Los respaldos no se editan, pero si se necesita, podrías usar 'edit backups'
        return $user->can('create backups');
    }

    public function delete(User $user, Backup $backup): bool
    {
        return $user->can('delete backups');
    }

    public function restore(User $user, Backup $backup): bool
    {
        return $user->can('restore backups');
    }
}