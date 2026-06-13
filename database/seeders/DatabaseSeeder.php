<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            SecurityQuestionsSeeder::class,
            RolePermissionSeeder::class,
            PositionSeeder::class,
            RankSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);
    }
}