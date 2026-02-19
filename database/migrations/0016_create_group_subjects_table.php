<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('group_subjects');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Schema::create('group_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('failed_subject_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->text('subject_name');
            $table->foreignId('semester_code')->constrained('semesters')->cascadeOnDelete();
            $table->double('credit')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_subjects');
    }
};
