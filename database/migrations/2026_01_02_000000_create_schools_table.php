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
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->id();
                $table->string('nama_sekolah');
                $table->decimal('latitude', 11, 8); // Koordinat sekolah (-180 to 180)
                $table->decimal('longitude', 11, 8); // Koordinat sekolah (-90 to 90)
                $table->integer('radius'); // Radius area sekolah dalam meter
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
