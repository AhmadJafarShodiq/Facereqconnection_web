<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'nama_mapel',
        'kelas_id',
    ];

    public $timestamps = true;

    // RELASI GURU YANG MENGAJAR MAPEL INI
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'user_id');
    }

    // RELASI KELAS
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // RELASI JADWAL PELAJARAN
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
