<?php

namespace App\Models;
use App\Models\Kelas;

use Illuminate\Database\Eloquent\Model;


class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'kelas_id',
        'nama_lengkap',
        'nip_nis',
        'jabatan_kelas',
        'instansi',
        'foto',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        if (!$this->foto) return null;
        return url('foto_profile/' . $this->foto);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function kelas()
{
    return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
}
    
}

