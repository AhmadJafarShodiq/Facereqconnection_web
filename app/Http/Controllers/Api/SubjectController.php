<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ================= SISWA =================
        if ($user->role === 'siswa') {

            if (!$user->profile || !$user->profile->kelas_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas siswa belum ditentukan'
                ], 422);
            }

            $subjects = Subject::where('kelas_id', $user->profile->kelas_id)
                ->select('id', 'nama_mapel')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $subjects
            ]);
        }

        // ================= GURU =================
        if ($user->role === 'guru') {

            $subjects = $user->subjects()
                ->select('subjects.id', 'subjects.nama_mapel', 'subjects.kelas_id')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $subjects
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Role tidak diizinkan'
        ], 403);
    }
}
