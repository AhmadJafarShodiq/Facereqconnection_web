<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // CEK KOLOM SUDAH ADA
            if (!Schema::hasColumn('subjects', 'kelas_id')) {
                $table->foreignId('kelas_id')
                    ->after('id')
                    ->constrained('classes')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};

