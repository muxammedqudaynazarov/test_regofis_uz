<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edu_years', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->index()->primary();
            $table->string('name');
            $table->enum('status', ['0', '1'])->default('0');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edu_years');
    }
};
