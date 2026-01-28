<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'nama_kelas',
    ];

    public $timestamps = true;

    // RELASI SISWA
    public function students()
    {
        return $this->hasMany(Profile::class, 'kelas_id');
    }

    // RELASI MAPEL
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'kelas_id');
    }

    // RELASI ATTENDANCE
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'kelas_id');
    }

    // RELASI JADWAL PELAJARAN
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'kelas_id');
    }
}
