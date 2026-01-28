<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
    ];

    // RELASI KE GURU (USER)
    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // RELASI KE MAPEL (SUBJECT)
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // RELASI KE KELAS
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
