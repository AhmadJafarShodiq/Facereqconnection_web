php artisan db:seed<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('teacher_subjects')) {
            Schema::create('teacher_subjects', function (Blueprint $table) {
                $table->id();
                
                // FOREIGN KEY KE USERS TABLE (GURU)
                $table->foreignId('user_id')
                      ->constrained('users')
                      ->onDelete('cascade');
                
                // FOREIGN KEY KE SUBJECTS TABLE (MAPEL)
                $table->foreignId('subject_id')
                      ->constrained('subjects')
                      ->onDelete('cascade');
                
                // TIMESTAMPS (CREATED_AT, UPDATED_AT)
                $table->timestamps();

                // UNIQUE CONSTRAINT: GURU TIDAK BISA MENGAJAR MAPEL YANG SAMA 2x
                $table->unique(['user_id', 'subject_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};