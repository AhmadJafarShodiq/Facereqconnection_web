<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;

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

        // AMBIL JADWAL BERDASARKAN KELAS SISWA
        $schedules = Schedule::where('kelas_id', $user->profile->kelas_id)
            ->with(['guru.profile', 'subject'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari')
            ->map(fn($items) => $items->map(fn($s) => [
                'id' => $s->id,
                'mapel' => $s->subject->nama_mapel,
                'guru' => $s->guru->profile->nama_lengkap,
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

        // AMBIL JADWAL MENGAJAR GURU
        $schedules = Schedule::where('user_id', $user->id)
            ->with(['subject', 'kelas'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari')
            ->map(fn($items) => $items->map(fn($s) => [
                'id' => $s->id,
                'mapel' => $s->subject->nama_mapel,
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
     * GET JADWAL PER HARI (untuk Flutter show real-time)
     */
public function todaySchedule(Request $request)
{
    $user = $request->user();

    $hariMap = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Minggu',
    ];

    $hariIni = $hariMap[now()->format('l')];

    $query = Schedule::with('subject')
        ->where('hari', $hariIni);

    // SISWA → filter kelas
    if ($user->role === 'siswa') {
        $query->where('kelas_id', $user->profile->kelas_id);
    }

    // GURU → filter user_id
    if ($user->role === 'guru') {
        $query->where('user_id', $user->id);
    }

    return response()->json([
        'status' => true,
        'hari'   => $hariIni,
        'data'   => $query->get(),
    ]);
}

}
