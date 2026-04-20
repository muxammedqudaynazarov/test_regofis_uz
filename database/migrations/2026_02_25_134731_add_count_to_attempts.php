<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('attempt')->after('archived')->default(1);
        });
    }

    public function down(): void
    {
        //
    }
};
