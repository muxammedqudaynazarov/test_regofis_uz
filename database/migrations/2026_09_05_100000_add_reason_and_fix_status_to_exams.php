<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. status ENUM ni to'liq qiymatlar bilan yangilash
        DB::statement("ALTER TABLE exams MODIFY COLUMN status ENUM('0','1','2','3','4','5','6','7','8') NOT NULL DEFAULT '0'");

        // 2. reason ustunini qo'shish
        Schema::table('exams', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('archived');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
        DB::statement("ALTER TABLE exams MODIFY COLUMN status ENUM('0','1','2') NOT NULL DEFAULT '0'");
    }
};
