<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'latitude',
        'longitude',
        'foto',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date', // ⬅️ PENTING
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
