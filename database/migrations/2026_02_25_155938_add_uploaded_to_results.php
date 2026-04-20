<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->enum('uploaded', ['0', '1'])->after('point')->default('0');
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            //
        });
    }
};
