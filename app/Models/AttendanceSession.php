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

// App\Models\AttendanceSession.php

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
    return $this->belongsTo(Kelas::class, 'kelas_id');
}

}