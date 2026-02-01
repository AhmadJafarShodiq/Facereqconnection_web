<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Subject;

class GuruAttendanceSessionController extends Controller
{
    public function open(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id'
        ]);

        $guru = $request->user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $subject = Subject::findOrFail($request->subject_id);

        // TUTUP SEMUA SESI AKTIF GURU
        AttendanceSession::where([
            'guru_id' => $guru->id,
            'is_active' => true
        ])->update([
            'is_active' => false,
            'ended_at' => now()
        ]);

        // BUKA SESI BARU
        $session = AttendanceSession::create([
            'guru_id'    => $guru->id,
            'subject_id' => $subject->id,
            'kelas_id'   => $subject->kelas_id,
            'started_at'=> now(),
            'is_active' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Sesi absen dibuka',
            'data' => [
                'session_id' => $session->id,
                'mapel' => $subject->nama_mapel,
                'kelas_id' => $subject->kelas_id,
                'started_at' => $session->started_at->format('H:i')
            ]
        ]);
    }

    public function close($id, Request $request)
    {
        $guru = $request->user();

        $session = AttendanceSession::where([
            'id' => $id,
            'guru_id' => $guru->id,
            'is_active' => true
        ])->first();

        if (!$session) {
            return response()->json([
                'status'=>false,
                'message'=>'Sesi tidak ditemukan / sudah ditutup'
            ], 404);
        }

        $session->update([
            'is_active' => false,
            'ended_at' => now()
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Sesi absen ditutup'
        ]);
    }
}
