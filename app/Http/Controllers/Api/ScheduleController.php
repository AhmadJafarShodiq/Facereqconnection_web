<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /* =====================================================
    | GET JADWAL SISWA
    ===================================================== */
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
                'guru' => optional($s->guru->profile)->nama_lengkap ?? '-',
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'ruangan' => $s->ruangan,
            ]));

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    /* =====================================================
    | GET JADWAL GURU
    ===================================================== */
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

    /* =====================================================
    | TODAY SCHEDULE (FINAL FIXED)
    ===================================================== */
    public function todaySchedule(Request $request)
    {
        $user = $request->user();

        $hariIni = match (Carbon::now()->dayOfWeekIso) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        };

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

            // 🔥 CEK SESI AKTIF & EXPIRED
            $session = AttendanceSession::where('schedule_id', $s->id)
                ->where('kelas_id', $s->kelas_id)
                ->where('subject_id', $s->subject_id)
                ->where('is_active', 1)
                ->first();

            if ($session && $session->ended_at && $session->ended_at->isPast()) {
                $session->update(['is_active' => 0]);
                $session = null;
            }

            $item = [
                'id' => $s->id,
                'subject_id' => $s->subject_id,
                'schedule_id' => $s->id,
                'name' => $s->subject->nama_mapel ?? '-',
                'jam_mulai' => $s->jam_mulai,
                'jam_selesai' => $s->jam_selesai,
                'ruangan' => $s->ruangan,
                'session_open' => $session !== null,
                'session_id' => $session?->id,
                'remaining_seconds' => $session && $session->ended_at ? now()->diffInSeconds($session->ended_at, false) : null,
            ];

            if ($user->role === 'guru') {
                $item['kelas'] = $s->kelas->nama_kelas ?? '-';
                $item['class_id'] = $s->kelas_id;
                
                // 🔥 TAMBAH STATISTIK SISWA (Filter Hanya Siswa Aktif di Kelas Ini)
                $item['total_students'] = $s->kelas_id 
                    ? \App\Models\Profile::where('kelas_id', $s->kelas_id)
                        ->whereHas('user', function($q) {
                            $q->where('role', 'siswa')->where('is_active', 1);
                        })->count()
                    : 0;
                $item['present_count'] = $session 
                    ? Attendance::where('attendance_session_id', $session->id)->count()
                    : 0;
            }

            if ($user->role === 'siswa') {

                $item['guru'] = optional($s->guru->profile)->nama_lengkap ?? '-';

                // 🔥 CEK SUDAH ABSEN BERDASARKAN SESSION
                $item['attended'] = $session
                    ? Attendance::where('user_id', $user->id)
                        ->where('attendance_session_id', $session->id)
                        ->exists()
                    : false;
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
