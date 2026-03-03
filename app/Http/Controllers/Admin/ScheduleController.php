<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Subject;
use App\Models\Kelas;
use PhpOffice\PhpSpreadsheet\IOFactory;

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


public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
    $rows = $spreadsheet->getActiveSheet()->toArray();

    $inserted = 0;
    $skipped  = 0;

    foreach ($rows as $index => $row) {

        if ($index == 0) continue;

        $hari        = $row[0] ?? null;
        $jam_mulai   = $row[1] ?? null;
        $jam_selesai = $row[2] ?? null;
        $nama_mapel  = $row[3] ?? null;
        $nama_guru   = $row[4] ?? null;
        $nama_kelas  = $row[5] ?? null;
        $ruangan     = $row[6] ?? null;

        if (!$hari || !$nama_mapel || !$nama_guru || !$nama_kelas) {
            $skipped++;
            continue;
        }

        $subject = Subject::where('nama_mapel', $nama_mapel)->first();
        $guru = User::where('role','guru')
                    ->whereHas('profile', function($q) use ($nama_guru){
                        $q->where('nama_lengkap', $nama_guru);
                    })->first();
        $kelas = Kelas::where('nama_kelas', $nama_kelas)->first();

        if (!$subject || !$guru || !$kelas) {
            $skipped++;
            continue;
        }

        Schedule::create([
            'user_id'     => $guru->id,
            'subject_id'  => $subject->id,
            'kelas_id'    => $kelas->id,
            'hari'        => $hari,
            'jam_mulai'   => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'ruangan'     => $ruangan,
        ]);

        $inserted++;
    }

    return back()->with('success',
        "Import selesai. $inserted jadwal ditambahkan, $skipped dilewati."
    );
}

public function deleteAll()
{
    Schedule::truncate(); // hapus semua data + reset auto increment

    return redirect()
        ->route('admin.schedules.index')
        ->with('success', 'Semua jadwal berhasil dihapus');
}
}
