<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    
    protected $table = 'attendance_sessions';

    protected $fillable = [
        'schedule_id',
        'guru_id',
        'kelas_id',
        'subject_id',
        'is_active',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'is_active'  => 'boolean',
    ];
}
