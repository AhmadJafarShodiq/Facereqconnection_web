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
        'schedule_id' => 'required|exists:schedules,id',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'foto' => 'nullable|image'
    ]);

    $user = $request->user();
    if ($user->role !== 'siswa')
        return response()->json(['status' => false], 403);

    $session = AttendanceSession::where([
        'schedule_id' => $request->schedule_id,
        'is_active' => true
    ])->first();

    if (!$session)
        return response()->json([
            'status' => false,
            'message' => 'Absen belum dibuka guru'
        ], 403);

    if (Attendance::where([
        'user_id' => $user->id,
        'attendance_session_id' => $session->id
    ])->exists())
        return response()->json([
            'status' => false,
            'message' => 'Sudah absen'
        ], 409);

    $path = $request->hasFile('foto')
        ? $request->file('foto')->store('absensi', 'public')
        : null;

   $schedule = Schedule::find($request->schedule_id); // <- ini baru

Attendance::create([
    'user_id' => $user->id,
    'role' => 'siswa',
    'tanggal' => now()->toDateString(),
    'jam_masuk' => now(),
    'attendance_session_id' => $session->id,
    'schedule_id' => $request->schedule_id,
    'subject_id' => $schedule->subject_id, // <- ini tambahan
    'kelas_id' => $user->profile->kelas_id,
    'latitude' => $request->latitude,
    'longitude' => $request->longitude,
    'foto' => $path,
    'status' => 'hadir'
]);

    return response()->json([
        'status' => true,
        'message' => 'Presensi berhasil'
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
   /* =====================================================
| SISWA HISTORY (DENGAN JAM PULANG, FOTO, DAN LOKASI)
===================================================== */
public function studentHistory(Request $request)
{
    $history = Attendance::where('user_id', $request->user()->id)
        ->with('subject')
        ->orderByDesc('jam_masuk')
        ->get()
        ->map(fn($a) => [
            'tanggal'   => $a->tanggal?->format('Y-m-d') ?? '-',
            'jam'       => $a->jam_masuk?->format('H:i') ?? '-',
            'time_out'  => $a->jam_pulang?->format('H:i') ?? '-',
            'status'    => $a->status ?? '-',
            'subject'   => $a->subject?->nama_mapel ?? '-',
            'latitude'  => $a->latitude ?? null,
            'longitude' => $a->longitude ?? null,
            'foto'      => $a->foto ? asset('storage/' . $a->foto) : null,
        ]);

    return response()->json([
        'status' => true,
        'data' => $history,
    ]);
}



    /* =====================================================
    | TODAY
    ===================================================== */
   public function today(Request $request)
{
    $today = Carbon::today()->toDateString();

    $attendance = Attendance::where('user_id', $request->user()->id)
        ->where('tanggal', $today)
        ->first();

    if (!$attendance) {
        return response()->json([
            'check_in'  => null,
            'check_out' => null,
        ]);
    }

    return response()->json([
        'check_in'  => $attendance->jam_masuk,
        'check_out' => $attendance->jam_pulang,
        'status'    => $attendance->status,
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
