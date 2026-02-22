<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Subject;
use App\Models\Kelas;

class DashboardController extends Controller
{
    public function index()
    {
        // ================= MASTER DATA =================
        $totalGuru   = User::where('role','guru')->count();
        $totalSiswa  = User::where('role','siswa')->count();
        $totalKelas  = Kelas::count();
        $totalMapel  = Subject::count();

        // ================= SESI =================
        $sesiAktif = AttendanceSession::where('is_active',1)->count();

        $totalSesiBulanIni = AttendanceSession::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->count();

        // ================= ABSENSI HARI INI =================
        $hadirHariIni = Attendance::whereDate('tanggal', today())
                            ->where('status','hadir')
                            ->count();

        $tidakHadirHariIni = Attendance::whereDate('tanggal', today())
                                ->where('status','tidak_hadir')
                                ->count();

        $terlambatHariIni = Attendance::whereDate('tanggal', today())
                                ->where('status','terlambat')
                                ->count();

        $totalAbsensiHariIni = Attendance::whereDate('tanggal', today())->count();

        // Hitung persentase hadir
        $persentaseHadir = $totalAbsensiHariIni > 0 
            ? round(($hadirHariIni / $totalAbsensiHariIni) * 100, 1)
            : 0;

        // ================= DATA TABEL =================
        $kelasSedangBerlangsung = AttendanceSession::with(['guru.profile','subject','kelas'])
                                ->where('is_active',1)
                                ->get();

        $logAktivitas = Attendance::with(['user.profile'])
                            ->latest()
                            ->limit(5)
                            ->get();

        return view('admin.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'totalMapel',
            'sesiAktif',
            'totalSesiBulanIni',
            'hadirHariIni',
            'tidakHadirHariIni',
            'terlambatHariIni',
            'totalAbsensiHariIni',
            'persentaseHadir',
            'kelasSedangBerlangsung',
            'logAktivitas'
        ));
    }
}
