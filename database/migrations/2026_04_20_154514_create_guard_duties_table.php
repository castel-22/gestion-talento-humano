<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guard_duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_rotation_id')->constrained('guard_rotations')->cascadeOnDelete();
            $table->date('date');
            $table->enum('letter', ['A', 'B', 'C', 'D']);
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['guard_rotation_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_duties');
    }
};