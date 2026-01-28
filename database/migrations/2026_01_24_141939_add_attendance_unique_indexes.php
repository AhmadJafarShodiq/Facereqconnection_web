<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('attendances', function (Blueprint $table) {

        // SISWA: 1 mapel 1 kali per hari
        $table->unique(
            ['user_id','tanggal','subject_id'],
            'uniq_student_attendance'
        );

        // GURU: 1 kali per hari
        $table->unique(
            ['user_id','tanggal'],
            'uniq_teacher_attendance'
        );
    });
}


    /**
     * Reverse the migrations.
     */
 public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropUnique('uniq_student_attendance');
        $table->dropUnique('uniq_teacher_attendance');
    });
}

};
