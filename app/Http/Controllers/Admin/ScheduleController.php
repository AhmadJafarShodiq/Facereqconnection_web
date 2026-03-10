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

        $kelasBentrok = Schedule::where('hari', $data['hari'])
            ->where('kelas_id', $data['kelas_id'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
            ->exists();

        if ($kelasBentrok) {
            return back()->withErrors('Jadwal bentrok dengan kelas lain')->withInput();
        }

        $guruBentrok = Schedule::where('hari', $data['hari'])
            ->where('user_id', $data['user_id'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
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

        $bentrok = Schedule::where('id', '!=', $schedule->id)
            ->where('hari', $data['hari'])
            ->where(function ($q) use ($data) {
                $q->where('kelas_id', $data['kelas_id'])
                  ->orWhere('user_id', $data['user_id']);
            })
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai'])
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
    $errors   = [];

    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // lewati header
        if (empty(array_filter($row))) continue; // lewati baris kosong

        $hari        = trim($row[0] ?? '');
        $jam_mulai   = trim($row[1] ?? '');
        $jam_selesai = trim($row[2] ?? '');
        $nama_mapel  = trim($row[3] ?? '');
        $nama_guru   = trim($row[4] ?? '');
        $nama_kelas  = trim($row[5] ?? '');
        $ruangan     = trim($row[6] ?? '');

        // Validasi data wajib
        if (!$hari || !$nama_mapel || !$nama_guru || !$nama_kelas || !$jam_mulai || !$jam_selesai) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap";
            continue;
        }

        $subject = Subject::where('nama_mapel', $nama_mapel)->first();
        $guru = User::where('role','guru')
                    ->whereHas('profile', function($q) use ($nama_guru){
                        $q->where('nama_lengkap', 'like', "%$nama_guru%");
                    })->first();
        $kelas = Kelas::where('nama_kelas', $nama_kelas)->first();

        if (!$subject) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Mapel '$nama_mapel' tidak ditemukan";
            continue;
        }
        if (!$guru) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Guru '$nama_guru' tidak ditemukan";
            continue;
        }
        if (!$kelas) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Kelas '$nama_kelas' tidak ditemukan";
            continue;
        }

        // Format waktu agar valid H:i:s
        try {
            $formatted_mulai = date('H:i:s', strtotime($jam_mulai));
            $formatted_selesai = date('H:i:s', strtotime($jam_selesai));
        } catch (\Exception $e) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Format waktu tidak valid";
            continue;
        }

        // Cek bentrok sederhana
        $bentrok = Schedule::where('hari', $hari)
            ->where(function($q) use ($guru, $kelas) {
                $q->where('user_id', $guru->id)
                  ->orWhere('kelas_id', $kelas->id);
            })
            ->where('jam_mulai', '<', $formatted_selesai)
            ->where('jam_selesai', '>', $formatted_mulai)
            ->exists();

        if ($bentrok) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Jadwal bentrok (Guru/Kelas)";
            continue;
        }

        Schedule::create([
            'user_id'     => $guru->id,
            'subject_id'  => $subject->id,
            'kelas_id'    => $kelas->id,
            'hari'        => $hari,
            'jam_mulai'   => $formatted_mulai,
            'jam_selesai' => $formatted_selesai,
            'ruangan'     => $ruangan,
        ]);

        $inserted++;
    }

    $msg = "Import selesai. $inserted jadwal ditambahkan, $skipped dilewati.";
    if (!empty($errors)) {
        session()->flash('import_errors', array_slice($errors, 0, 10));
    }

    return back()->with('success', $msg);
}

public function downloadTemplate()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $headers = ['hari', 'jam_mulai', 'jam_selesai', 'nama_mapel', 'nama_guru', 'nama_kelas', 'ruangan'];
    foreach ($headers as $key => $header) {
        $sheet->setCellValue([$key + 1, 1], $header);
    }

    // Contoh Data
    $exampleData = [
        ['Senin', '07:00', '08:00', 'Matematika', 'Budi Santoso', 'X RPL 1', 'Lab 1'],
        ['Senin', '08:00', '09:00', 'Bahasa Indonesia', 'Ani Wijaya', 'X RPL 1', 'R.05'],
    ];

    foreach ($exampleData as $rowKey => $rowData) {
        foreach ($rowData as $colKey => $cellValue) {
            $sheet->setCellValue([$colKey + 1, $rowKey + 2], $cellValue);
        }
    }

    // Auto size
    foreach (range('A', 'G') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="template_import_jadwal.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
public function deleteAll()
{
    Schedule::truncate(); // hapus semua data + reset auto increment

    return redirect()
        ->route('admin.schedules.index')
        ->with('success', 'Semua jadwal berhasil dihapus');
}
}
