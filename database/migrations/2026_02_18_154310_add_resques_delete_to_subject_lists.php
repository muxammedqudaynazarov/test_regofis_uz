<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subject_lists', function (Blueprint $table) {
            // 0 - standart, 1 - yuborilgan, 2 - bekor qilindi
            $table->enum('request_delete', ['0', '1', '2', '3', '4', '5'])->after('semester_id')->default('0');
        });
    }

    public function down(): void
    {
        //
    }
};
