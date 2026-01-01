<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceData extends Model
{
    protected $table = 'face_data';

    protected $fillable = [
        'user_id',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
