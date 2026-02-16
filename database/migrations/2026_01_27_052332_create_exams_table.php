<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('test_id')->constrained('tests')->cascadeOnDelete();
            $table->enum('status', ['0', '1', '2', '3', '4'])->default('0');
            $table->timestamp('finished_at')->nullable();
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
