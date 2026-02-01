<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
    'guru_id',
    'subject_id',
    'kelas_id',
    'schedule_id',
    'started_at',
    'ended_at',
    'is_active',
];


    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    
    public function guru()
{
    return $this->belongsTo(User::class, 'guru_id');
}

public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function kelas()
{
    return $this->belongsTo(Kelas::class);
}
public function attendances()
{
    return $this->hasMany(Attendance::class);
}
}
