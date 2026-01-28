<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // ← dari Sanctum

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

        // ================= GURU & ADMIN =================
        if (in_array($user->role, ['guru', 'admin'])) {

            if (!$user->profile || !$user->profile->kelas_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas guru belum ditentukan'
                ], 422);
            }

            $subjects = Subject::where('kelas_id', $user->profile->kelas_id)
                ->select('id', 'nama_mapel', 'kelas_id')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $subjects
            ]);
        }

        // ================= ROLE LAIN =================
        return response()->json([
            'status' => false,
            'message' => 'Role tidak diizinkan'
        ], 403);
    }
}
