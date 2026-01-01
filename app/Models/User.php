<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    // RELASI
    public function profile()
    {
         return $this->hasOne(Profile::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function faceData()
{
    return $this->hasOne(FaceData::class);
}



}
