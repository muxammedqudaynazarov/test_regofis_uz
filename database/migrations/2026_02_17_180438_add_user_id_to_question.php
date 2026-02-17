<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('question', function (Blueprint $table) {
            $table->foreignId('user_id')->after('question_text')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
    }
};
