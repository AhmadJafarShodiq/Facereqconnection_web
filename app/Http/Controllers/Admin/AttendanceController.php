<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user.profile','subject','kelas'])
            ->orderByDesc('tanggal');

        // Filter User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // ✅ Filter Role (guru / siswa)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $attendances = $query->paginate(15)->withQueryString();
        $users = User::orderBy('username')->get();

        return view('admin.attendance.index', compact('attendances','users'));
    }


    public function show(Attendance $attendance)
    {
        $attendance->load(['user.profile','subject','kelas']);
        return view('admin.attendance.show', compact('attendance'));
    }


    public function export(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $roleFilter = $request->input('role');

        $daysInMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->daysInMonth;

        // Ambil semua data absensi di bulan & tahun tsb
        $allAttendance = Attendance::whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // ================= REKAP GURU =================
        $rekapGuru = collect();
        if (!$roleFilter || $roleFilter == 'guru') {
            $gurus = User::with('profile')->where('role', 'guru')->where('is_active', 1)->get();
            $rekapGuru = $gurus->map(function ($user) use ($allAttendance, $daysInMonth) {
                $userAttendance = $allAttendance->where('user_id', $user->id);
                $days = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $item = $userAttendance->first(fn($i) => $i->tanggal->day == $d);
                    if ($item) {
                        if ($item->status == 'hadir') $days[$d] = 'H';
                        elseif ($item->status == 'terlambat') $days[$d] = 'T';
                        elseif ($item->status == 'pulang') $days[$d] = 'P';
                        elseif ($item->status == 'pulang_dini') $days[$d] = 'PD';
                        else $days[$d] = 'H';
                    } else {
                        $days[$d] = '-';
                    }
                }

                return [
                    'nama'  => $user->profile->nama_lengkap ?? $user->username,
                    'days'  => $days,
                    'hadir' => $userAttendance->whereIn('status', ['hadir', 'pulang', 'pulang_dini'])->count(),
                    'terlambat' => $userAttendance->where('status', 'terlambat')->count(),
                    'total' => $userAttendance->count(),
                ];
            });
        }

        // ================= REKAP SISWA =================
        $rekapSiswa = collect();
        if (!$roleFilter || $roleFilter == 'siswa') {
            $classes = \App\Models\Kelas::with(['students.user.profile'])->get();
            $rekapSiswa = $classes->map(function ($kelas) use ($allAttendance, $daysInMonth) {
                $siswaData = $kelas->students->map(function ($profile) use ($allAttendance, $daysInMonth) {
                    $user = $profile->user;
                    if (!$user || $user->role !== 'siswa' || !$user->is_active) return null;

                    $userAttendance = $allAttendance->where('user_id', $user->id);
                    $days = [];
                    for ($d = 1; $d <= $daysInMonth; $d++) {
                        $item = $userAttendance->first(fn($i) => $i->tanggal->day == $d);
                        if ($item) {
                            if ($item->status == 'hadir') $days[$d] = 'H';
                            elseif ($item->status == 'terlambat') $days[$d] = 'T';
                            else $days[$d] = 'H';
                        } else {
                            $days[$d] = '-';
                        }
                    }

                    return [
                        'nama'  => $profile->nama_lengkap ?? $user->username,
                        'days'  => $days,
                        'hadir' => $userAttendance->where('status', 'hadir')->count(),
                        'terlambat' => $userAttendance->where('status', 'terlambat')->count(),
                        'total' => $userAttendance->count(),
                    ];
                })->filter();

                return [
                    'nama_kelas' => $kelas->nama_kelas,
                    'siswa' => $siswaData
                ];
            })->filter(fn($k) => $k['siswa']->count() > 0);
        }

        $pdf = Pdf::loadView('admin.attendance.export', [
            'rekapGuru'   => $rekapGuru,
            'rekapSiswa'  => $rekapSiswa,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'daysInMonth' => $daysInMonth,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap_absensi_' . $bulan . '_' . $tahun . '.pdf');
    }
}
