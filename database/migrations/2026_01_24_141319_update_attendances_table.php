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

        if (!Schema::hasColumn('attendances','role')) {
            $table->enum('role',['siswa','guru'])->after('user_id');
        }

        if (!Schema::hasColumn('attendances','subject_id')) {
            $table->unsignedBigInteger('subject_id')
                  ->nullable()
                  ->after('tanggal');
        }

        if (!Schema::hasColumn('attendances','kelas_id')) {
            $table->unsignedBigInteger('kelas_id')
                  ->nullable()
                  ->after('subject_id');
        }

        if (!Schema::hasColumn('attendances','jam_pulang')) {
            $table->time('jam_pulang')
                  ->nullable()
                  ->after('jam_masuk');
        }
    });

    Schema::table('attendances', function (Blueprint $table) {
        $table->enum('status',[
            'hadir','terlambat','pulang','pulang_dini'
        ])->change();
    });
}
public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropColumn(['role','subject_id','kelas_id','jam_pulang']);
    });
}
};
