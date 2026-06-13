<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $row) {
            $row->id();
            $row->foreignId('user_id')->constrained()->onDelete('cascade');
            $row->string('action'); // ej: 'create', 'update', 'delete', 'login'
            $row->string('module'); // ej: 'employees', 'vacations'
            $row->string('description');
            $row->json('changes')->nullable(); // Para guardar el antes y después
            $row->string('ip_address')->nullable();
            $row->string('user_agent')->nullable();
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
