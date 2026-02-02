<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceSession;
use App\Models\Schedule;

class GuruAttendanceSessionController extends Controller
{

/* =====================================================
| BUKA SESI ABSEN (BERDASARKAN JADWAL)
===================================================== */
public function open(Request $request)
{
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id'
    ]);

    $guru = $request->user();

    if ($guru->role !== 'guru') {
        return response()->json([
            'status'=>false,
            'message'=>'Unauthorized'
        ],403);
    }

    $schedule = Schedule::with(['subject','kelas'])
        ->where('id',$request->schedule_id)
        ->where('user_id',$guru->id)
        ->first();

    if (!$schedule) {
        return response()->json([
            'status'=>false,
            'message'=>'Jadwal tidak ditemukan'
        ],404);
    }

    /* ===== TUTUP SEMUA SESI AKTIF GURU ===== */
    AttendanceSession::where([
        'guru_id'=>$guru->id,
        'is_active'=>true
    ])->update([
        'is_active'=>false,
        'ended_at'=>now()
    ]);

    /* ===== BUKA SESI BARU ===== */
    $session = AttendanceSession::create([
        'guru_id'    => $guru->id,
        'schedule_id'=> $schedule->id,
        'subject_id' => $schedule->subject_id,
        'kelas_id'   => $schedule->kelas_id,
        'started_at'=> now(),
        'is_active' => true
    ]);

    return response()->json([
        'status'=>true,
        'message'=>'Sesi absen dibuka',
        'data'=>[
            'session_id'=>$session->id,
            'mapel'=>$schedule->subject->nama_mapel,
            'kelas'=>$schedule->kelas->nama_kelas ?? '-',
            'jam_mulai'=>$schedule->jam_mulai,
            'jam_selesai'=>$schedule->jam_selesai,
            'started_at'=>$session->started_at->format('H:i')
        ]
    ]);
}


/* =====================================================
| TUTUP SESI ABSEN
===================================================== */
public function close($id, Request $request)
{
    $guru = $request->user();

    if ($guru->role !== 'guru') {
        return response()->json([
            'status'=>false,
            'message'=>'Unauthorized'
        ],403);
    }

    $session = AttendanceSession::where([
        'id'=>$id,
        'guru_id'=>$guru->id,
        'is_active'=>true
    ])->first();

    if (!$session) {
        return response()->json([
            'status'=>false,
            'message'=>'Sesi tidak ditemukan / sudah ditutup'
        ],404);
    }

    $session->update([
        'is_active'=>false,
        'ended_at'=>now()
    ]);

    return response()->json([
        'status'=>true,
        'message'=>'Sesi absen ditutup'
    ]);
}

}
