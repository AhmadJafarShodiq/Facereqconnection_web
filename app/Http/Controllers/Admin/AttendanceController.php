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
        $query = Attendance::with('user.profile')->orderByDesc('tanggal');

        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->tanggal)
            $query->whereDate('tanggal', $request->tanggal);

        $attendances = $query->get();
        $users = User::all();

        return view('admin.attendance.index', compact('attendances', 'users'));
    }

    public function show(Attendance $attendance)
    {
        return view('admin.attendance.show', compact('attendance'));
    }

    public function export(Request $request)
    {
        $query = Attendance::with('user.profile')->orderByDesc('tanggal');

        if ($request->user_id)
            $query->where('user_id', $request->user_id);
        if ($request->tanggal)
            $query->whereDate('tanggal', $request->tanggal);

        $attendances = $query->get();

        $pdf = Pdf::loadView('admin.attendance.export', compact('attendances'));
        return $pdf->download('attendance_' . now()->format('Y-m-d') . '.pdf');
    }
}
