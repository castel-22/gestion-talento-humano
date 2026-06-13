<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guard_rotations', function (Blueprint $table) {
            $table->foreignId('employee_a_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employee_b_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employee_c_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('employee_d_id')->nullable()->constrained('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guard_rotations', function (Blueprint $table) {
            $table->dropForeign(['employee_a_id']);
            $table->dropForeign(['employee_b_id']);
            $table->dropForeign(['employee_c_id']);
            $table->dropForeign(['employee_d_id']);
            $table->dropColumn(['employee_a_id', 'employee_b_id', 'employee_c_id', 'employee_d_id']);
        });
    }
};
