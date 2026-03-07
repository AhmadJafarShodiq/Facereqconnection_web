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
        'logo',
        'primary_color',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('assets/images/logosmk.png'); // Default logo
    }
}
