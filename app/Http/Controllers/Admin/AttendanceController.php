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
    $query = Attendance::with(['user.profile','kelas'])
        ->orderBy('role')
        ->orderBy('tanggal');

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('tanggal', $request->tahun);
    }

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    $data = $query->get();

    // ================= REKAP GURU =================
$rekapGuru = $data->where('role','guru')
    ->groupBy('user_id')
    ->map(function ($items) {

        $hadir = $items->filter(function ($i) {
            return in_array($i->status, [
                'hadir',
                'terlambat',
                'pulang',
                'pulang_dini'
            ]);
        })->count();

        return [
            'nama'  => $items->first()->user->profile->nama_lengkap
                        ?? $items->first()->user->username,
            'hadir' => $hadir,
            'izin'  => 0,
            'sakit' => 0,
            'alpha' => 0,
            'total' => $items->count(),
        ];
    });
 $rekapSiswa = $data->where('role','siswa')
    ->groupBy('kelas_id')
    ->map(function ($kelasItems) {

        return [
            'nama_kelas' => optional($kelasItems->first()->kelas)->nama_kelas ?? '-',

            'siswa' => $kelasItems->groupBy('user_id')
                ->map(function ($items) {

                    $hadir = $items->where('status','hadir')->count();
                    $terlambat = $items->where('status','terlambat')->count();

                    return [
                        'nama'  => $items->first()->user->profile->nama_lengkap
                                    ?? $items->first()->user->username,
                        'hadir' => $hadir,
                        'terlambat' => $terlambat,
                        'total' => $items->count(),
                    ];
                })
        ];
    });  $pdf = Pdf::loadView('admin.attendance.export', [
        'rekapGuru' => $rekapGuru,
        'rekapSiswa' => $rekapSiswa,
        'bulan' => $request->bulan,
        'tahun' => $request->tahun,
    ]);

    return $pdf->download('rekap_absensi.pdf');
}
}
