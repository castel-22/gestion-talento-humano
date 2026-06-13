<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'view vacations', 'create vacations', 'edit vacations', 'delete vacations',
            'view attendances', 'create attendances', 'edit attendances', 'delete attendances',
            'view deployments', 'create deployments', 'edit deployments', 'delete deployments',
            'view backups', 'create backups', 'delete backups', 'restore backups',
            'view schedules', 'create schedules', 'edit schedules', 'delete schedules',
            'view leaves', 'create leaves', 'edit leaves', 'delete leaves',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $secretaria = Role::firstOrCreate(['name' => 'secretaria', 'guard_name' => 'web']);

        // Asignar todos los permisos al administrador
        $admin->syncPermissions(Permission::all());

        // Asignar permisos al supervisor (todos los que necesita)
        $supervisor->syncPermissions([
            'view users', 'create users', 'edit users', 'delete users',
            'view departments', 'create departments', 'edit departments', 'delete departments',
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'view vacations', 'create vacations', 'edit vacations', 'delete vacations',
            'view attendances', 'create attendances', 'edit attendances', 'delete attendances',
            'view deployments', 'create deployments', 'edit deployments', 'delete deployments',
            'view backups', 'create backups', 'delete backups', 'restore backups',
            'view schedules', 'create schedules', 'edit schedules', 'delete schedules',
            'view leaves', 'create leaves', 'edit leaves', 'delete leaves',
        ]);

        // Asignar permisos a la secretaria (limitados)
        $secretaria->syncPermissions([
            'view departments', 'create departments', 'edit departments',
            'view employees', 'create employees', 'edit employees',
            'view vacations', 'create vacations',
            'view attendances', 'create attendances',
            'view deployments', 'create deployments', 'edit deployments', 'delete deployments',
            'view schedules', 'create schedules', 'edit schedules', 'delete schedules',
            'view leaves', 'create leaves',
        ]);
    }
}