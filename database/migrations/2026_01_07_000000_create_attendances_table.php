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
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->enum('role', ['siswa', 'guru'])->default('siswa');
                $table->date('tanggal');
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->cascadeOnDelete();
                $table->foreignId('kelas_id')->nullable()->constrained('classes')->cascadeOnDelete();
                $table->time('jam_masuk')->nullable();
                $table->time('jam_pulang')->nullable();
                $table->decimal('latitude', 11, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('foto')->nullable();
                $table->enum('status', ['hadir', 'terlambat', 'pulang', 'pulang_dini'])->default('hadir');
                $table->timestamps();

                // INDEXES
                $table->index(['user_id', 'tanggal']);
                $table->index(['tanggal']);
                $table->index(['subject_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
