<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\AttendanceSession;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * GET JADWAL SISWA (berdasarkan kelas siswa)
     */
    public function studentSchedule(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'siswa') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        if (!$user->profile || !$user->profile->kelas_id) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas siswa belum ditentukan'
            ], 422);
        }

        $schedules = Schedule::where('kelas_id', $user->profile->kelas_id)
            ->with(['guru.profile', 'subject'])
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari')
            ->map(fn($items) => $items->map(fn($s) => [
                'id' => $s->id,
                'mapel' => $s->subject->nama_mapel ?? '-',
                'guru' => $s->guru->profile->nama_lengkap ?? '-',
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'ruangan' => $s->ruangan,
            ]));

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    /**
     * GET JADWAL GURU (jadwal mengajar guru)
     */
    public function teacherSchedule(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'guru') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $schedules = Schedule::where('user_id', $user->id)
            ->with(['subject', 'kelas'])
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari')
            ->map(fn($items) => $items->map(fn($s) => [
                'id' => $s->id,
                'mapel' => $s->subject->nama_mapel ?? '-',
                'kelas' => $s->kelas->nama_kelas ?? '-',
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'ruangan' => $s->ruangan,
            ]));

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    /**
     * GET JADWAL PER HARI (Flutter real-time)
     */
    public function todaySchedule(Request $request)
    {
        $user = $request->user();
        $hariIni = Carbon::now()->locale('id')->translatedFormat('l');

        $query = Schedule::with(['subject', 'kelas', 'guru.profile'])
            ->where('hari', $hariIni);

        if ($user->role === 'siswa') {
            if (!$user->profile || !$user->profile->kelas_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas siswa belum ditentukan'
                ], 422);
            }
            $query->where('kelas_id', $user->profile->kelas_id);
        }

        if ($user->role === 'guru') {
            $query->where('user_id', $user->id);
        }
        $schedules = $query->orderBy('jam_mulai')->get()->map(function ($s) use ($user) {
            $item = [
                'id' => $s->id,
                'name' => $s->subject->nama_mapel ?? '-',
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'ruangan' => $s->ruangan,
            ];

            if ($user->role === 'guru') {
                $session = AttendanceSession::where([
                    'schedule_id' => $s->id,
                    'is_active' => true
                ])->first();

                $item['kelas'] = $s->kelas->nama_kelas ?? '-';
                $item['session_open'] = $session !== null;
                $item['session_id'] = $session?->id;
            }

            if ($user->role === 'siswa') {
                $item['guru'] = $s->guru->profile->nama_lengkap ?? '-';
                $item['attended'] = false;
            }

            return $item;
        });

        return response()->json([
            'status' => true,
            'hari' => $hariIni,
            'data' => $schedules
        ]);
    }
}
