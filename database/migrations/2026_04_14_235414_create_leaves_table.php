<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('duration_value'); // cantidad
            $table->string('duration_unit'); // days, weeks, months
            $table->string('doctor_name');
            $table->string('issuing_institution');
            $table->text('medical_condition')->nullable();
            $table->enum('status', ['pendiente', 'aprobado', 'rechazado', 'finalizado'])->default('pendiente');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaves');
    }
};