<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->foreignId('user_id')->after('uploaded')->nullable()->constrained('users')->cascadeOnDelete();
        });
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('user_id')->after('semester_id')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        //
    }
};
