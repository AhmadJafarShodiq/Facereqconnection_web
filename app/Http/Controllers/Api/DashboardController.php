<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

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

            $history = Attendance::where('user_id',$user->id)
                ->orderByDesc('jam_masuk')
                ->limit(5)
                ->get()
                ->map(fn($a)=>[
                    'mapel' => optional($a->subject)->nama_mapel ?? '-',
                    'jam'   => optional($a->jam_masuk)->format('H:i'),
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
}
