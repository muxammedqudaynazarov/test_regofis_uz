<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('exams');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subject_lists')->cascadeOnDelete();
            $table->unsignedBigInteger('failed_subject_id');
            $table->foreign('failed_subject_id')->references('failed_subject_id')->on('group_subjects')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->enum('status', ['0', '1', '2', '3', '4'])->default('0');
            // 0 - baslanbagan, 1 - processte, 2 - juwmaqlagan, 3 - juwmaqlangan, 4 - sessiya uzilgen
            $table->timestamp('last_activity_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
