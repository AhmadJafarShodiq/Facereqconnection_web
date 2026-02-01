<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use App\Models\AttendanceSession;

class AttendanceController extends Controller
{
    /* =========================
     * SISWA - PRESENSI MAPEL
     * ========================= */
    public function studentCheckIn(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'foto'       => 'nullable|image'
        ]);

        $user = $request->user();

        if ($user->role !== 'siswa') {
            return response()->json(['status'=>false,'message'=>'Unauthorized'],403);
        }

        // CEK PROFILE SISWA
        if (!$user->profile || !$user->profile->kelas_id) {
            return response()->json([
                'status'=>false,
                'message'=>'Data kelas siswa tidak lengkap'
            ],422);
        }

        // CEK SUBJECT SESUAI KELAS SISWA
        $subject = Subject::find($request->subject_id);
        if (!$subject || $subject->kelas_id !== $user->profile->kelas_id) {
            return response()->json([
                'status'=>false,
                'message'=>'Mapel tidak sesuai dengan kelas Anda'
            ],403);
        }

        $today = Carbon::today()->toDateString();
        $now   = now();

        // CEK SUDAH ABSEN MAPEL INI HARI INI
        if (Attendance::where([
            'user_id'    => $user->id,
            'tanggal'    => $today,
            'subject_id' => $request->subject_id
        ])->exists()) {
            return response()->json([
                'status'=>false,
                'message'=>'Sudah absen mapel ini hari ini'
            ],409);
        }

        // VALIDASI LOKASI
        $school = School::first();
        if ($school) {
            $distance = $this->distance(
                $school->latitude,
                $school->longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > $school->radius) {
                return response()->json([
                    'status'=>false,
                    'message'=>'Di luar area sekolah'
                ],403);
            }
        }

        $jamTerlambat = Carbon::createFromTime(8,15);
        $status = $now->lte($jamTerlambat) ? 'hadir' : 'terlambat';

        $path = $request->hasFile('foto')
            ? $request->file('foto')->store('absensi','public')
            : null;

        
$session = AttendanceSession::where([
    'subject_id' => $request->subject_id,
    'kelas_id'   => $user->profile->kelas_id,
    'is_active'  => true
])->latest('started_at')->first();

if (!$session) {
    return response()->json([
        'status'=>false,
        'message'=>'Absen belum dibuka oleh guru'
    ], 403);
}
    
    
    
            $attendance = Attendance::create([
            'user_id'    => $user->id,
            'role'       => 'siswa',
            'tanggal'    => $today,
            'jam_masuk'  => $now,
            'subject_id' => $request->subject_id,
            'kelas_id'   => $user->profile->kelas_id,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'foto'       => $path,
            'status'     => $status,
            'attendance_session_id' => $session->id,

        ]);

        $attendance->load('subject');

        return response()->json([
            'status'=>true,
            'message'=>'Presensi berhasil',
            'data'=>[
                'nama'   => $user->profile->nama_lengkap,
                'kelas'  => $user->profile->kelas_id,
                'mapel'  => $attendance->subject->nama_mapel ?? '-',
                'jam'    => $attendance->jam_masuk->format('H:i'),
                'status' => $attendance->status,
                'foto'   => $attendance->foto,
                'lat'    => $attendance->latitude,
                'lng'    => $attendance->longitude
            ]
        ]);
    }

    /* =========================
     * GURU - MASUK SEKOLAH
     * ========================= */
    public function teacherCheckIn(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();

        if ($user->role !== 'guru') {
            return response()->json(['status'=>false,'message'=>'Unauthorized'],403);
        }

        // CEK PROFILE GURU
        if (!$user->profile) {
            return response()->json([
                'status'=>false,
                'message'=>'Data profil guru tidak lengkap'
            ],422);
        }

        $today = Carbon::today()->toDateString();

        // CEK SUDAH PRESENSI HARI INI
        if (Attendance::where('user_id',$user->id)->where('tanggal',$today)->first()) {
            return response()->json([
                'status'=>false,
                'message'=>'Sudah presensi hari ini'
            ],409);
        }

        // VALIDASI LOKASI (OPTIONAL)
        $school = School::first();
        if ($school) {
            $distance = $this->distance(
                $school->latitude,
                $school->longitude,
                $request->latitude,
                $request->longitude
            );

            if ($distance > $school->radius) {
                return response()->json([
                    'status'=>false,
                    'message'=>'Di luar area sekolah'
                ],403);
            }
        }

        $jamTerlambat = Carbon::createFromTime(8,15);
        $status = now()->lte($jamTerlambat) ? 'hadir' : 'terlambat';

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'role'      => 'guru',
            'tanggal'   => $today,
            'jam_masuk' => now(),
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'status'    => $status
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Presensi masuk berhasil',
            'data'=>$attendance
        ]);
    }

    /* =========================
     * GURU - PULANG
     * ========================= */
    public function teacherCheckOut(Request $request)
    {
        $attendance = Attendance::where('user_id',$request->user()->id)
            ->where('tanggal',Carbon::today()->toDateString())
            ->first();

        if (!$attendance || $attendance->jam_pulang) {
            return response()->json([
                'status'=>false,
                'message'=>'Belum check-in / sudah pulang'
            ],409);
        }

        $attendance->update([
            'jam_pulang'=>now(),
            'status'=>'pulang'
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Presensi pulang berhasil'
        ]);
    }

// =========================
// GURU - SISWA BELUM ABSEN
// =========================
public function missingStudents(Request $request, $subjectId)
{
    $user = $request->user();
    if ($user->role !== 'guru') {
        return response()->json(['status' => false], 403);
    }

    $today   = now()->toDateString();
    $kelasId = $user->profile->kelas_id;

    $students = User::where('role', 'siswa')
        ->whereHas('profile', fn($q) => $q->where('kelas_id', $kelasId))
        ->with('profile')
        ->get();

    $presentIds = Attendance::where('tanggal', $today)
        ->where('subject_id', $subjectId)
        ->pluck('user_id')
        ->toArray();

    $missing = $students
        ->whereNotIn('id', $presentIds)
        ->map(fn($s) => [
            'id'   => $s->id,
            'nama' => $s->profile->nama_lengkap,
            'nis'  => $s->profile->nip_nis,
        ])
        ->values();

    return response()->json([
        'status' => true,
        'data'   => $missing
    ]);
}

// =========================
// GURU - SISWA SUDAH ABSEN
// =========================
public function studentAttendanceBySubject(Request $request, $subjectId)
{
    $user = $request->user();
    if ($user->role !== 'guru') {
        return response()->json(['status' => false], 403);
    }

    $today = now()->toDateString();

    $data = Attendance::where('tanggal', $today)
        ->where('subject_id', $subjectId)
        ->with('user.profile')
        ->get()
        ->map(fn($a) => [
            'id'   => $a->user->id,
            'nama' => $a->user->profile->nama_lengkap,
            'nis'  => $a->user->profile->nip_nis,
        ]);

    return response()->json([
        'status' => true,
        'data'   => $data
    ]);
}

    /* =========================
     * GURU - REPORT ABSENSI (RANGE TANGGAL)
     * ========================= */
    public function attendanceReport(Request $request, $subjectId)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date'
        ]);

        $user = $request->user();

        if ($user->role !== 'guru') {
            return response()->json(['status'=>false,'message'=>'Unauthorized'],403);
        }

        // CEK GURU MENGAJAR MAPEL INI
        $subject = Subject::find($subjectId);
        if (!$subject || !$subject->teachers->contains($user->id)) {
            return response()->json([
                'status'=>false,
                'message'=>'Anda tidak mengajar mapel ini'
            ],403);
        }

        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        // AMBIL SEMUA SISWA DI KELAS INI
        $students = User::where('role','siswa')
            ->whereHas('profile', fn($q)=>$q->where('kelas_id',$subject->kelas_id))
            ->with(['profile'])
            ->get();

            $allAttendance = Attendance::where('subject_id', $subjectId)
    ->whereBetween('tanggal', [$startDate, $endDate])
    ->orderBy('tanggal')
    ->get()
    ->groupBy('user_id');

        // GROUPING ABSENSI PER SISWA
     $report = $students->map(function ($student) use ($allAttendance) {

    $records = $allAttendance[$student->id] ?? collect();

    return [
        'id'   => $student->id,
        'nama' => $student->profile->nama_lengkap,
        'nis'  => $student->profile->nip_nis,
        'attendance' => $records->map(fn($a) => [
            'tanggal'   => $a->tanggal->format('Y-m-d'),
            'jam_masuk' => $a->jam_masuk?->format('H:i'),
            'status'    => $a->status,
        ])->values()
    ];
});

        // STATISTIK KESELURUHAN
        $totalDays = Attendance::where('subject_id', $subjectId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->distinct('tanggal')
            ->count('tanggal');

        return response()->json([
            'status'=>true,
            'subject' => $subject->nama_mapel,
            'periode' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'total_hari' => $totalDays,
            'total_siswa' => $students->count(),
            'statistik' => [
                'hadir' => Attendance::where('subject_id', $subjectId)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('status', 'hadir')
                    ->count(),
                'terlambat' => Attendance::where('subject_id', $subjectId)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('status', 'terlambat')
                    ->count(),
                'belum_absen' => ($students->count() * $totalDays) - (
                    Attendance::where('subject_id', $subjectId)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->count()
                )
            ],
            'report' => $report
        ]);
    }
  public function today(Request $request)
{
    $user = $request->user();
    $today = Carbon::today()->toDateString();

    $attendance = Attendance::where('user_id', $user->id)
    ->where('tanggal', $today)
    ->with('subject')
    ->latest('jam_masuk')
    ->first();

    if (!$attendance) return response()->json([]);

   return response()->json([
    'status' => true,
    'data' => [
        'check_in_time' => $attendance->jam_masuk?->format('H:i'),
        'is_late'       => $attendance->status === 'terlambat',
       'late_minutes' => $attendance->status === 'terlambat' && $attendance->jam_masuk
    ? Carbon::parse('08:15')->diffInMinutes($attendance->jam_masuk)
    : 0,

        'subject'       => $attendance->subject?->nama_mapel,
    ]
]);
}


    /* =========================
     * DISTANCE (meter)
     * ========================= */
    private function distance($lat1,$lon1,$lat2,$lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2))
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
              * cos(deg2rad($theta));
        return acos($dist) * 60 * 1.1515 * 1609.344;
    }

  public function studentHistory(Request $request)
{
    $user = $request->user();
    $history = Attendance::where('user_id', $user->id)
        ->orderBy('tanggal', 'desc')
        ->with('subject')
        ->get()
        ->map(function($a){
    return [
        'tanggal'  => $a->tanggal->format('Y-m-d'),
        'jam'      => $a->jam_masuk?->format('H:i'),
        'time_out' => $a->jam_pulang?->format('H:i'),
        'status'   => $a->status,
        'subject'  => $a->subject?->nama_mapel ?? '-',
    ];
});


    return response()->json([
        'status' => true,
        'data'   => $history
    ]);
}


}
