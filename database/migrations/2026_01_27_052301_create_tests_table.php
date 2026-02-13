<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('durations')->default(50);
            $table->integer('questions')->default(50);
            $table->integer('attempts')->default(2);
            $table->enum('retest', ['y', 'n'])->default('n');
            $table->integer('points')->default(100);
            $table->enum('type', ['on', 'yn'])->default('on');
            $table->enum('status', ['0', '1', '2', '3', '4'])->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
