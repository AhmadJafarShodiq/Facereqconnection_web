<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Subject;
use App\Models\Kelas;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'subject_id',
        'kelas_id',
        'latitude',
        'longitude',
        'foto',
        'status',
        'attendance_session_id',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_masuk'  => 'datetime',
        'jam_pulang' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function session()
{
    return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
}

}
