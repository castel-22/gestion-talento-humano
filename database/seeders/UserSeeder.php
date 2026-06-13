<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Adrián Palacios (Administrador)
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'adrian@gmail.com'],
            [
                'name' => 'Adrian Palacios',
                'password' => \Illuminate\Support\Facades\Hash::make('adrian1'),
            ]
        );
        $admin->assignRole('administrador');

        // Empleado para Adrian
        \App\Models\Employee::firstOrCreate(
            ['email' => 'adrian@gmail.com'],
            [
                'first_name' => 'Adrian',
                'last_name' => 'Palacios',
                'id_number' => 'V-11111111',
                'personal_phone' => '0414-1111111',
                'status' => 'activo',
                'department_id' => 1,
                'position_id' => 1,
                'rank_id' => 1,
                'user_id' => $admin->id,
                'hired_date' => '2020-01-01',
                'position' => 'Director',
            ]
        );

        // 2. Laura Pérez (Supervisor)
        $supervisor = \App\Models\User::firstOrCreate(
            ['email' => 'laura@gmail.com'],
            [
                'name' => 'Laura Perez',
                'password' => \Illuminate\Support\Facades\Hash::make('laura123'),
            ]
        );
        $supervisor->assignRole('supervisor');

        \App\Models\Employee::firstOrCreate(
            ['email' => 'laura@gmail.com'],
            [
                'first_name' => 'Laura',
                'last_name' => 'Perez',
                'id_number' => 'V-22222222',
                'personal_phone' => '0414-2222222',
                'status' => 'activo',
                'department_id' => 2,
                'position_id' => 2,
                'rank_id' => 2,
                'user_id' => $supervisor->id,
                'hired_date' => '2020-01-01',
                'position' => 'Supervisor',
            ]
        );

        // 3. Carlos Gómez (Secretaria)
        $analista = \App\Models\User::firstOrCreate(
            ['email' => 'carlos@gmail.com'],
            [
                'name' => 'Carlos Gomez',
                'password' => \Illuminate\Support\Facades\Hash::make('carlos123'),
            ]
        );
        $analista->assignRole('secretaria');

        \App\Models\Employee::firstOrCreate(
            ['email' => 'carlos@gmail.com'],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Gomez',
                'id_number' => 'V-33333333',
                'personal_phone' => '0414-3333333',
                'status' => 'activo',
                'department_id' => 3,
                'position_id' => 3,
                'rank_id' => 3,
                'user_id' => $analista->id,
                'hired_date' => '2020-01-01',
                'position' => 'Secretaria',
            ]
        );

        // 4. María López (Secretaria)
        $empleado = \App\Models\User::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'name' => 'Maria Lopez',
                'password' => \Illuminate\Support\Facades\Hash::make('maria123'),
            ]
        );
        $empleado->assignRole('secretaria');

        \App\Models\Employee::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Lopez',
                'id_number' => 'V-44444444',
                'personal_phone' => '0414-4444444',
                'status' => 'activo',
                'department_id' => 1,
                'position_id' => 4,
                'rank_id' => 4,
                'user_id' => $empleado->id,
                'hired_date' => '2020-01-01',
                'position' => 'Secretaria',
            ]
        );
    }
}
