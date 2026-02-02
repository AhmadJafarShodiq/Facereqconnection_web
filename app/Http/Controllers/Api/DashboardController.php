<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /* =========================
     * DASHBOARD (SISWA & GURU)
     * ========================= */
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today()->toDateString();

        /* ======================
         * SISWA
         * ====================== */
        if ($user->role === 'siswa') {

            $hadir = Attendance::where('user_id',$user->id)
                ->where('tanggal',$today)
                ->where('status','hadir')
                ->count();

            $terlambat = Attendance::where('user_id',$user->id)
                ->where('tanggal',$today)
                ->where('status','terlambat')
                ->count();

            $history = Attendance::where('user_id',$user->id)
                ->with('subject')
                ->orderByDesc('jam_masuk')
                ->limit(5)
                ->get()
                ->map(fn($a)=>[
                    'mapel'  => $a->subject?->nama_mapel ?? '-',
                    'jam'    => $a->jam_masuk?->format('H:i'),
                    'status' => $a->status
                ]);

            return response()->json([
                'status' => true,
                'role'   => 'siswa',
                'nama'   => $user->profile->nama_lengkap,
                'summary'=> [
                    'hadir'     => $hadir,
                    'terlambat' => $terlambat
                ],
                'history'=> $history
            ]);
        }

        /* ======================
         * GURU
         * ====================== */
        if ($user->role === 'guru') {

            $attendance = Attendance::where('user_id',$user->id)
                ->where('tanggal',$today)
                ->first();

            return response()->json([
                'status' => true,
                'role'   => 'guru',
                'nama'   => $user->profile->nama_lengkap,
                'presensi'=>[
                    'masuk'  => $attendance?->jam_masuk !== null,
                    'pulang' => $attendance?->jam_pulang !== null,
                    'jam_masuk'  => $attendance?->jam_masuk?->format('H:i'),
                    'jam_pulang' => $attendance?->jam_pulang?->format('H:i'),
                ]
            ]);
        }

        return response()->json([
            'status'=>false,
            'message'=>'Role tidak dikenali'
        ],400);
    }

    /* =========================https://chatgpt.com/c/69370732-306c-8322-9871-00f3bc2cbc77
     * DASHBOARD GURU HARI INI
     * ========================= */
    public function guruToday(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'guru') {
            return response()->json(['status'=>false],403);
        }

        $hariMap = [
            'Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu',
            'Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'
        ];

        $hariIni = $hariMap[now()->format('l')];

        $schedules = Schedule::with(['subject','kelas'])
            ->where('user_id',$user->id)
            ->where('hari',$hariIni)
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($s) {

                $session = AttendanceSession::where([
                    'schedule_id'=>$s->id,
                    'is_active'=>true
                ])->first();

                return [
                    'id'           => $s->id,
                    'mapel'        => $s->subject->nama_mapel,
                    'kelas'        => $s->kelas->nama_kelas ?? '-',
                    'jam_mulai'    => $s->jam_mulai,
                    'jam_selesai'  => $s->jam_selesai,
                    'session_open' => $session !== null,
                    'session_id'   => $session?->id
                ];
            });

        return response()->json([
            'status'=>true,
            'data'=>$schedules
        ]);
    }
}
