<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'nama_sekolah',
        'latitude',
        'longitude',
        'radius',
    ];
}
