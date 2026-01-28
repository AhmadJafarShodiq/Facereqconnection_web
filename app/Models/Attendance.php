<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Subject;

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
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // RELASI KELAS
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
