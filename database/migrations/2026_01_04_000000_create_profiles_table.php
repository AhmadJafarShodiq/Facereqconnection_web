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
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('nama_lengkap')->nullable();
                $table->string('nip_nis')->nullable();
                $table->string('jabatan_kelas')->nullable();
                $table->string('instansi')->nullable();
                $table->string('foto')->nullable();
                $table->foreignId('kelas_id')->nullable()->constrained('classes')->cascadeOnDelete();
                $table->timestamps();

                // INDEX
                $table->index(['user_id']);
                $table->index(['kelas_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
