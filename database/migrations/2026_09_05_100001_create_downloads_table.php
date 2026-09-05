<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('type');                                                    // export turi
            $table->string('name');                                                    // ko'rinish nomi
            $table->string('filename')->nullable();                                    // fayl nomi
            $table->string('path')->nullable();                                        // storage ichidagi yo'l
            $table->enum('status', ['pending', 'processing', 'ready', 'failed'])
                  ->default('pending');
            $table->text('reason')->nullable();                                        // xato sababi
            $table->json('metadata')->nullable();                                      // qo'shimcha parametrlar
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
