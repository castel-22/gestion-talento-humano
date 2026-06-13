<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SecurityQuestion;

class SecurityQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿Cuál es tu comida favorita?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el nombre de tu mejor amigo de la infancia?',
            '¿Cuál fue tu primera película vista en el cine?',
            '¿Cuál es el segundo nombre de tu madre?',
            '¿Cuál es tu deporte favorito?',
            '¿Cómo se llamaba tu profesor favorito en la escuela?',
        ];

        foreach ($questions as $q) {
            SecurityQuestion::create(['question' => $q]);
        }
    }
}