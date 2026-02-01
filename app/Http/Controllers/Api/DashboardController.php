<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\AttendanceSession;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today()->toDateString();

        // ======================
        // SISWA
        // ======================
        if ($user->role === 'siswa') {

            $hadir = Attendance::where([
                'user_id' => $user->id,
                'tanggal' => $today,
                'status'  => 'hadir'
            ])->count();

            $terlambat = Attendance::where([
                'user_id' => $user->id,
                'tanggal' => $today,
                'status'  => 'terlambat'
            ])->count();

        $history = Attendance::where('user_id', $user->id)
    ->with('subject') // ✅ WAJIB
    ->orderByDesc('jam_masuk')
    ->limit(5)
    ->get()
    ->map(fn($a)=>[
        'mapel' => $a->subject?->nama_mapel ?? '-',
        'jam'   => $a->jam_masuk?->format('H:i'),
        'status'=> $a->status
    ]);
    return response()->json([
                'role' => 'siswa',
                'nama' => $user->profile->nama_lengkap,
                'summary' => [
                    'hadir'     => $hadir,
                    'terlambat' => $terlambat
                ],
                'history' => $history
            ]);
        }

        // ======================
        // GURU
        // ======================
        if ($user->role === 'guru') {

            $attendance = Attendance::where([
                'user_id'=>$user->id,
                'tanggal'=>$today
            ])->first();

            return response()->json([
                'role' => 'guru',
                'nama' => $user->profile->nama_lengkap,
                'presensi' => [
                    'masuk'  => $attendance?->jam_masuk !== null,
                    'pulang' => $attendance?->jam_pulang !== null
                ]
            ]);
        }

        return response()->json([
            'message'=>'Role tidak dikenali'
        ],400);
    }


public function guruToday(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'guru') {
        return response()->json(['status'=>false], 403);
    }

    $hariMap = [
        'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
        'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
    ];

    $hariIni = $hariMap[now()->format('l')];

    $schedules = Schedule::with(['subject','kelas'])
        ->where('user_id', $user->id)
        ->where('hari', $hariIni)
        ->orderBy('jam_mulai')
        ->get()
        ->map(function ($s) {

            $session = AttendanceSession::where([
                'schedule_id' => $s->id,
                'is_active'   => true
            ])->first();

            return [
                'id' => $s->id,
                'name' => $s->subject->nama_mapel,
                'kelas' => $s->kelas->nama_kelas ?? '-',
                'session_open' => $session !== null,
                'session_id' => $session?->id
            ];
        });

    return response()->json($schedules);
}


}
