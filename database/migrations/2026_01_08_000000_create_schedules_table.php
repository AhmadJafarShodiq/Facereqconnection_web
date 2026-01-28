<?php

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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            
            // GURU YANG MENGAJAR
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            
            // MAPEL YANG DIAJARKAN
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
            
            // KELAS
            $table->foreignId('kelas_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            
            // HARI (SENIN-MINGGU)
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
            
            // JAM PELAJARAN
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            
            // RUANGAN (OPSIONAL)
            $table->string('ruangan')->nullable();
            
            $table->timestamps();

            // INDEX
            $table->index(['user_id']);
            $table->index(['subject_id']);
            $table->index(['kelas_id']);
            $table->index(['hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
