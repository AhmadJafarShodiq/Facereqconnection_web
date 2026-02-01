<?php

namespace App\Models;

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


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}

