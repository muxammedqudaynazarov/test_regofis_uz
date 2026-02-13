<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('workplaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('emp_type_id')->constrained('employee_types')->cascadeOnDelete();
            $table->foreignId('emp_staff_id')->constrained('employee_staff')->cascadeOnDelete();
            $table->foreignId('emp_form_id')->constrained('employee_forms')->cascadeOnDelete();
            $table->foreignId('emp_pos_id')->constrained('employee_positions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplaces');
    }
};
