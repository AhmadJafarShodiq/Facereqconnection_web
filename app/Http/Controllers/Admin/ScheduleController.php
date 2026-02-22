<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Kelas;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $schedules = Schedule::with(['guru.profile','subject','kelas'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('subject', function ($s) use ($search) {
                    $s->where('nama_mapel', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('admin.schedules.index', compact('schedules','search'));
    }

    public function create()
    {
        return view('admin.schedules.create', [
            'gurus'    => User::where('role','guru')->with('profile')->get(),
            'subjects' => Subject::all(),
            'kelas'    => Kelas::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'subject_id'  => 'required|exists:subjects,id',
            'kelas_id'    => 'required|exists:classes,id',
            'hari'        => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'ruangan'     => 'nullable|string'
        ]);

        // ===== CEK BENTROK KELAS =====
        $kelasBentrok = Schedule::where('hari', $data['hari'])
            ->where('kelas_id', $data['kelas_id'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('jam_mulai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('jam_mulai', '<=', $data['jam_mulai'])
                         ->where('jam_selesai', '>=', $data['jam_selesai']);
                  });
            })
            ->exists();

        if ($kelasBentrok) {
            return back()->withErrors('Jadwal bentrok dengan kelas lain')->withInput();
        }

        // ===== CEK BENTROK GURU =====
        $guruBentrok = Schedule::where('hari', $data['hari'])
            ->where('user_id', $data['user_id'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('jam_mulai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('jam_mulai', '<=', $data['jam_mulai'])
                         ->where('jam_selesai', '>=', $data['jam_selesai']);
                  });
            })
            ->exists();

        if ($guruBentrok) {
            return back()->withErrors('Guru sudah mengajar di jam tersebut')->withInput();
        }

        Schedule::create($data);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success','Jadwal berhasil ditambahkan');
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.edit', [
            'schedule' => $schedule,
            'gurus'    => User::where('role','guru')->with('profile')->get(),
            'subjects' => Subject::all(),
            'kelas'    => Kelas::all(),
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'subject_id'  => 'required|exists:subjects,id',
            'kelas_id'    => 'required|exists:classes,id',
            'hari'        => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'ruangan'     => 'nullable|string'
        ]);

        // ===== CEK BENTROK (EXCEPT ID INI) =====
        $bentrok = Schedule::where('id','!=',$schedule->id)
            ->where('hari', $data['hari'])
            ->where(function ($q) use ($data) {
                $q->where(function ($q2) use ($data) {
                    $q2->where('kelas_id', $data['kelas_id'])
                       ->orWhere('user_id', $data['user_id']);
                });
            })
            ->where(function ($q) use ($data) {
                $q->whereBetween('jam_mulai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhereBetween('jam_selesai', [$data['jam_mulai'], $data['jam_selesai']])
                  ->orWhere(function ($q2) use ($data) {
                      $q2->where('jam_mulai', '<=', $data['jam_mulai'])
                         ->where('jam_selesai', '>=', $data['jam_selesai']);
                  });
            })
            ->exists();

        if ($bentrok) {
            return back()->withErrors('Jadwal bentrok')->withInput();
        }

        $schedule->update($data);

        return redirect()
            ->route('admin.schedules.index')
            ->with('success','Jadwal berhasil diupdate');
    }
}
