<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('employee_type', ['gobernacion', 'alcaldia', 'homologado', 'nacional'])->nullable();
            $table->enum('gender', ['masculino', 'femenino', 'otro'])->nullable();
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('rank_id')->nullable()->constrained('ranks')->nullOnDelete();
            $table->string('institutional_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['rank_id']);
            $table->dropColumn(['employee_type', 'gender', 'position_id', 'rank_id', 'institutional_code']);
        });
    }
};