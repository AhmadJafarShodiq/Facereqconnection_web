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
        if (!Schema::hasTable('face_data')) {
            Schema::create('face_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->json('embedding'); // Array 192 float values dari Flutter
                $table->timestamps();

                // INDEX
                $table->index(['user_id']);
                $table->unique('user_id'); // 1 user = 1 face data
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_data');
    }
};
