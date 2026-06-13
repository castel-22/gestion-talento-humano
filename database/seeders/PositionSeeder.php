<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'Director Municipal / Director', 'level' => 'Directivo', 'has_grade' => false, 'order' => 1],
            ['name' => 'Subdirector', 'level' => 'Directivo', 'has_grade' => false, 'order' => 2],
            ['name' => 'Director de Talento Humano', 'level' => 'Directivo', 'has_grade' => false, 'order' => 3],
            ['name' => 'Administrador / Administradora', 'level' => 'Directivo', 'has_grade' => false, 'order' => 4],
            ['name' => 'Jefe de División', 'level' => 'Jefatura', 'has_grade' => false, 'order' => 5],
            ['name' => 'Jefe de Oficina', 'level' => 'Jefatura', 'has_grade' => false, 'order' => 6],
            ['name' => 'Coordinador Municipal (CGPC)', 'level' => 'Jefatura', 'has_grade' => false, 'order' => 7],
            ['name' => 'Coordinador de Protección Civil', 'level' => 'Jefatura', 'has_grade' => true, 'order' => 8],
            ['name' => 'Supervisor de Servicios Generales I', 'level' => 'Jefatura', 'has_grade' => false, 'order' => 9],
            ['name' => 'Oficial Supervisor', 'level' => 'Operativo', 'has_grade' => true, 'order' => 10],
            ['name' => 'Oficial de Protección Civil', 'level' => 'Operativo', 'has_grade' => true, 'order' => 11],
            ['name' => 'Oficial de Educación', 'level' => 'Operativo', 'has_grade' => false, 'order' => 12],
            ['name' => 'Oficial Operativo', 'level' => 'Operativo', 'has_grade' => false, 'order' => 13],
            ['name' => 'Médico', 'level' => 'Técnico', 'has_grade' => true, 'order' => 14],
            ['name' => 'Técnico de Atención Pre-Hospitalaria', 'level' => 'Técnico', 'has_grade' => true, 'order' => 15],
            ['name' => 'Enfermera', 'level' => 'Técnico', 'has_grade' => false, 'order' => 16],
            ['name' => 'Técnico de Intervención en Riesgos', 'level' => 'Técnico', 'has_grade' => true, 'order' => 17],
            ['name' => 'Instructor de Riesgo Urbano', 'level' => 'Técnico', 'has_grade' => true, 'order' => 18],
            ['name' => 'Técnico de Operaciones', 'level' => 'Técnico', 'has_grade' => false, 'order' => 19],
            ['name' => 'Despachador de Atención Pre-Hospitalaria', 'level' => 'Técnico', 'has_grade' => false, 'order' => 20],
            ['name' => 'Analista Administrativo', 'level' => 'Administrativo', 'has_grade' => true, 'order' => 21],
            ['name' => 'Analista de Registro y Control Estadístico III', 'level' => 'Administrativo', 'has_grade' => false, 'order' => 22],
            ['name' => 'Asistente Administrativo', 'level' => 'Administrativo', 'has_grade' => true, 'order' => 23],
            ['name' => 'Asistente Ejecutivo', 'level' => 'Administrativo', 'has_grade' => false, 'order' => 24],
            ['name' => 'Secretaria', 'level' => 'Administrativo', 'has_grade' => false, 'order' => 25],
            ['name' => 'Técnico en Conducción de Emergencias / Chofer', 'level' => 'Logístico', 'has_grade' => true, 'order' => 26],
            ['name' => 'Auxiliar de Operaciones', 'level' => 'Logístico', 'has_grade' => false, 'order' => 27],
            ['name' => 'Técnico de Transporte', 'level' => 'Logístico', 'has_grade' => false, 'order' => 28],
            ['name' => 'Obrera', 'level' => 'Logístico', 'has_grade' => false, 'order' => 29],
            ['name' => 'Temporal (Emergencia/Ciencias/Administrativa)', 'level' => 'Temporal', 'has_grade' => false, 'order' => 30],
            ['name' => 'Contratado', 'level' => 'Temporal', 'has_grade' => false, 'order' => 31],
        ];

        foreach ($positions as $pos) {
            Position::create($pos);
        }
    }
}