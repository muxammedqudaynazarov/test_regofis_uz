<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('retrains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('status', ['0', '1'])->default('0');
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('retrain_id')->after('education_year')->nullable()->constrained('retrains')->cascadeOnDelete();
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('retrain_id')->after('user_id')->nullable()->constrained('retrains')->cascadeOnDelete();
        });
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('retrain_id')->after('exam_id')->nullable()->constrained('retrains')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retrains');
    }
};
