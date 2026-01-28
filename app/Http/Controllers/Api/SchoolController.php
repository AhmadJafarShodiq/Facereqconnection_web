<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;

class SchoolController extends Controller
{
    public function index()
    {
        $school = School::first();

        if (!$school) {
            return response()->json([
                'status' => false,
                'message' => 'Data sekolah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $school->id,
                'nama_sekolah' => $school->nama_sekolah,
                'latitude' => $school->latitude,
                'longitude' => $school->longitude,
                'radius' => $school->radius, // bisa dipakai untuk validasi di Flutter
            ]
        ]);
    }
}
