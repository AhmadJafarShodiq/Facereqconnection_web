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
        $today = now()->format('l'); // Senin, Selasa, dst
        $hariMapping = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        $hari = $hariMapping[$today] ?? 'Senin';

        if ($user->role === 'siswa') {
            $schedules = Schedule::where('kelas_id', $user->profile->kelas_id)
                ->where('hari', $hari)
                ->with(['guru.profile', 'subject'])
                ->orderBy('jam_mulai')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'mapel' => $s->subject->nama_mapel,
                    'guru' => $s->guru->profile->nama_lengkap,
                    'jam_mulai' => $s->jam_mulai,
                    'jam_selesai' => $s->jam_selesai,
                    'ruangan' => $s->ruangan,
                ]);
        } else if ($user->role === 'guru') {
            $schedules = Schedule::where('user_id', $user->id)
                ->where('hari', $hari)
                ->with(['subject', 'kelas'])
                ->orderBy('jam_mulai')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'mapel' => $s->subject->nama_mapel,
                    'kelas' => $s->kelas->nama_kelas ?? '-',
                    'jam_mulai' => $s->jam_mulai,
                    'jam_selesai' => $s->jam_selesai,
                    'ruangan' => $s->ruangan,
                ]);
        } else {
            $schedules = [];
        }

        return response()->json([
            'status' => true,
            'hari' => $hari,
            'data' => $schedules
        ]);
    }
}
