<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    public function run()
    {
        $ranks = [
            ['name' => 'Oficial Supervisor III', 'abbreviation' => 'OSPC III', 'order' => 1],
            ['name' => 'Oficial Supervisor II', 'abbreviation' => 'OSPC II', 'order' => 2],
            ['name' => 'Oficial Supervisor I', 'abbreviation' => 'OSPC I', 'order' => 3],
            ['name' => 'Oficial de Protección Civil III', 'abbreviation' => 'OPC III', 'order' => 4],
            ['name' => 'Oficial de Protección Civil II', 'abbreviation' => 'OPC II', 'order' => 5],
            ['name' => 'Oficial de Protección Civil I', 'abbreviation' => 'OPC I', 'order' => 6],
        ];

        foreach ($ranks as $rank) {
            Rank::create($rank);
        }
    }
}