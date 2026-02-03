<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    /* =====================================================
    | SISWA CHECK-IN MAPEL
    ===================================================== */
    public function studentCheckIn(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'nullable|image'
        ]);

        $user = $request->user();

        if ($user->role !== 'siswa')
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);

        if (!$user->profile || !$user->profile->kelas_id)
            return response()->json(['status' => false, 'message' => 'Data kelas tidak lengkap'], 422);

        $today = now()->toDateString();
        $now = now();

        /* ===== VALIDASI MAPEL ===== */
        $subject = Subject::find($request->subject_id);

        if (!$subject || $subject->kelas_id !== $user->profile->kelas_id)
            return response()->json(['status' => false, 'message' => 'Mapel tidak sesuai kelas'], 403);

        /* ===== VALIDASI JAM PELAJARAN ===== */
        $schedule = Schedule::where('subject_id', $subject->id)
            ->where('kelas_id', $user->profile->kelas_id)
            ->where('hari', now()->translatedFormat('l'))
            ->whereTime('jam_mulai', '<=', $now)
            ->whereTime('jam_selesai', '>=', $now)
            ->first();

        if (!$schedule)
            return response()->json(['status' => false, 'message' => 'Bukan jam pelajaran'], 403);

        /* ===== CEK SESSION ===== */
        $session = AttendanceSession::where([
            'schedule_id' => $schedule->id,
            'is_active' => true
        ])->first();

        if (!$session)
            return response()->json(['status' => false, 'message' => 'Absen belum dibuka guru'], 403);

        /* ===== SUDAH ABSEN ===== */
        if (
            Attendance::where([
                'user_id' => $user->id,
                'tanggal' => $today,
                'subject_id' => $subject->id
            ])->exists()
        )
            return response()->json(['status' => false, 'message' => 'Sudah absen'], 409);

        /* ===== VALIDASI LOKASI ===== */
        $school = School::first();
        if ($school) {
            $distance = $this->distance(
                $school->latitude,
                $school->longitude,
                $request->latitude,
                $request->longitude
            );
            if ($distance > $school->radius)
                return response()->json(['status' => false, 'message' => 'Di luar area sekolah'], 403);
            if ($distance < $school->radius && !$request->hasFile('foto'))
                return response()->json(['status' => true, 'message' => 'Di dalam area sekolah'], 403);
        }

        /* ===== STATUS ===== */
        $jamTerlambat = Carbon::createFromTime(8, 15);
        $status = $now->lte($jamTerlambat) ? 'hadir' : 'terlambat';

        /* ===== FOTO ===== */
        $path = $request->hasFile('foto')
            ? $request->file('foto')->store('absensi', 'public')
            : null;


        /* ===== SIMPAN ===== */
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'role' => 'siswa',
            'tanggal' => $today,
            'jam_masuk' => $now,
            'subject_id' => $subject->id,
            'kelas_id' => $user->profile->kelas_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto' => $path,
            'status' => $status,
            'attendance_session_id' => $session->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Presensi berhasil',
            'data' => [
                'nama' => $user->profile->nama_lengkap,
                'mapel' => $subject->nama_mapel,
                'jam' => $attendance->jam_masuk->format('H:i'),
                'status' => $status
            ]
        ]);
    }


    /* =====================================================
    | GURU CHECK-IN
    ===================================================== */
    public function teacherCheckIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $user = $request->user();

        if ($user->role !== 'guru')
            return response()->json(['status' => false], 403);

        $today = Carbon::today()->toDateString();

        if (Attendance::where('user_id', $user->id)->where('tanggal', $today)->exists())
            return response()->json(['status' => false, 'message' => 'Sudah presensi'], 409);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'role' => 'guru',
            'tanggal' => $today,
            'jam_masuk' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'hadir'
        ]);

        return response()->json(['status' => true, 'data' => $attendance]);
    }


    /* =====================================================
    | GURU CHECK-OUT
    ===================================================== */
    public function teacherCheckOut(Request $request)
    {
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$attendance || $attendance->jam_pulang)
            return response()->json(['status' => false], 409);

        $attendance->update([
            'jam_pulang' => now(),
            'status' => 'pulang'
        ]);

        return response()->json(['status' => true]);
    }


    /* =====================================================
    | SISWA HISTORY
    ===================================================== */
    public function studentHistory(Request $request)
    {
        $history = Attendance::where('user_id', $request->user()->id)
            ->with('subject')
            ->orderByDesc('jam_masuk')
            ->get()
            ->map(fn($a) => [
                'tanggal' => $a->tanggal->format('Y-m-d'),
                'jam' => $a->jam_masuk?->format('H:i'),
                'status' => $a->status,
                'subject' => $a->subject?->nama_mapel ?? '-'
            ]);

        return response()->json(['status' => true, 'data' => $history]);
    }


    /* =====================================================
    | TODAY
    ===================================================== */
    public function today(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('tanggal', $today)
            ->with('subject')
            ->first();
        if (!$attendance)
            return response()->json([]);

        return response()->json([
            'check_in_time' => $attendance->jam_masuk?->format('H:i'),
            'subject' => $attendance->subject?->nama_mapel
        ]);
    }


    /* =====================================================
    | GURU – SISWA BELUM ABSEN (FIX ROUTE ERROR)
    ===================================================== */
    public function missingStudents($subjectId, $classId, Request $request)
    {
        $guru = $request->user();
        if ($guru->role !== 'guru')
            return response()->json(['status' => false], 403);

        $today = now()->toDateString();

        $subject = Subject::with('kelas.students')->findOrFail($subjectId);

        $absenUserIds = Attendance::where('subject_id', $subjectId)
            ->where('kelas_id', $classId)
            ->where('tanggal', $today)
            ->pluck('user_id')
            ->toArray();

        $missing = $subject->kelas->students
            ->filter(fn($p) => !in_array($p->user_id, $absenUserIds))
            ->map(fn($p) => [
                'user_id' => $p->user_id,
                'nama' => $p->nama_lengkap
            ]);
        return response()->json(['status' => true, 'data' => $missing]);
    }


    /* =====================================================
    | GURU – ABSENSI MAPEL HARI INI
    ===================================================== */
    public function studentAttendanceBySubject($subjectId, $classId)
    {
        $today = now()->toDateString();

        $data = Attendance::with('user.profile')
            ->where('subject_id', $subjectId)
            ->where('kelas_id', $classId)
            ->where('tanggal', $today)
            ->get()
            ->map(fn($a) => [
                'nama' => $a->user->profile->nama_lengkap ?? '-',
                'jam' => $a->jam_masuk?->format('H:i'),
                'status' => $a->status
            ]);

        return response()->json(['status' => true, 'data' => $data]);
    }


    /* =====================================================
    | GURU – REPORT MAPEL
    ===================================================== */
    public function attendanceReport($subjectId, $classId)
    {
        $today = now()->toDateString();

        return response()->json([
            'status' => true,
            'summary' => [
                'hadir' => Attendance::where('subject_id', $subjectId)
                    ->where('kelas_id', $classId)
                    ->where('tanggal', $today)
                    ->where('kelas_id', $classId)
                    ->where('status', 'hadir')
                    ->count(),
                'terlambat' => Attendance::where('subject_id', $subjectId)
                    ->where('kelas_id', $classId)
                    ->where('tanggal', $today)
                    ->where('status', 'terlambat')
                    ->count(),
            ]
        ]);
    }


    /* =====================================================
    | DISTANCE (HAVERSINE)
    ===================================================== */
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return $earthRadius * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

}
