<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar el campo status de ENUM a VARCHAR(50) para mayor flexibilidad
        Schema::table('vacations', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });
    }

    public function down(): void
    {
        // Revertir a ENUM (solo si fuera necesario, no se recomienda)
        Schema::table('vacations', function (Blueprint $table) {
            $table->enum('status', [
                'pendiente',
                'aprobado',
                'en_curso',
                'interrumpido',
                'finalizado',
                'rechazado',
                'reanudado'
            ])->change();
        });
    }
};