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

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // ✅ Tambahan Filter Role saat export juga
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $data = $query->get();

        $attendances = [
            'guru' => $data->where('role', 'guru')
                ->groupBy(fn ($i) => $i->tanggal->format('Y-m')),

            'siswa' => $data->where('role', 'siswa')
                ->groupBy([
                    'kelas_id',
                    fn ($i) => $i->tanggal->format('Y-m')
                ]),
        ];

        $pdf = Pdf::loadView('admin.attendance.export', compact('attendances'));

        return $pdf->download('rekap_absensi.pdf');
    }
}
