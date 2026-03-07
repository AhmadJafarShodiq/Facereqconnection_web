<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    // LIST USER
   public function index(Request $request)
{
    $search = $request->search;
    $role   = $request->role;

    $users = User::with('profile.kelas')
        ->when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%$search%");
        })
        ->when($role, function ($query) use ($role) {
            $query->where('role', $role);
        })
        ->orderBy('id','asc')
        ->paginate(10)
        ->withQueryString();

    return view('admin.users.index', compact('users','search','role'));
}


    // FORM TAMBAH USER
    public function create()
    {
        return view('admin.users.create');
    }

    // SIMPAN USER BARU
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:4',
            'role' => 'required|in:admin,guru,siswa'

        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'is_active'=> 1
        ]);

        return redirect()->route('admin.users.index')->with('success','User berhasil ditambahkan');
    }

    // FORM EDIT USER
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // UPDATE USER
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,'.$user->id,
             'role' => 'required|in:admin,guru,siswa'

        ]);

        $user->update([
            'username' => $request->username,
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success','User berhasil diupdate');
    }

    // DETAIL USER
  public function show(User $user)
{
    $user->load('profile.kelas');
    return view('admin.users.show', compact('user'));
}


    public function toggle(User $user)
{
    $user->update([
        'is_active' => !$user->is_active
    ]);

    return back()->with('success','Status user diubah');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    $file = $request->file('file');
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $inserted = 0;
    $skipped  = 0;
    $errors   = [];

    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // skip header
        
        // Skip truly empty rows
        if (empty(array_filter($row))) continue;

        $username       = trim($row[0] ?? '');
        $role           = strtolower(trim($row[1] ?? ''));
        $password       = trim($row[2] ?? '123456');
        $nama_lengkap   = trim($row[3] ?? '');
        $nip_nis        = trim($row[4] ?? '');
        $instansi       = trim($row[5] ?? '');
        $kelas_id       = $row[6] ?? null;
        $jabatan_kelas  = trim($row[7] ?? '');

        // Validasi dasar
        if (!$username || !$role) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Username atau Role kosong";
            continue;
        }

        if (!in_array($role, ['admin','guru','siswa'])) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Role '$role' tidak valid (harus admin/guru/siswa)";
            continue;
        }

        // Cek username sudah ada
        if (\App\Models\User::where('username', $username)->exists()) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": Username '$username' sudah terdaftar";
            continue;
        }

        // Hanya cek kelas kalau role siswa dan kelas_id ada
        if ($role === 'siswa' && $kelas_id && !DB::table('classes')->where('id', $kelas_id)->exists()) {
            $skipped++;
            $errors[] = "Baris " . ($index + 1) . ": ID Kelas '$kelas_id' tidak ditemukan";
            continue;
        }

        $user = \App\Models\User::create([
            'username'  => $username,
            'password'  => \Illuminate\Support\Facades\Hash::make($password ?: '123456'),
            'role'      => $role,
            'is_active' => 1
        ]);

        $user->profile()->create([
            'nama_lengkap'  => $nama_lengkap ?: null,
            'nip_nis'       => $nip_nis ?: null,
            'instansi'      => $instansi ?: null,
            'kelas_id'      => $kelas_id ?: null,
            'jabatan_kelas' => $jabatan_kelas ?: null,
        ]);

        $inserted++;
    }

    $msg = "Import selesai. $inserted user ditambahkan, $skipped dilewati.";
    if (!empty($errors)) {
        session()->flash('import_errors', array_slice($errors, 0, 10)); // limit 10 errors to avoid session bloat
    }

    return redirect()->route('admin.users.index')->with('success', $msg);
}

public function downloadTemplate()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $headers = ['username', 'role', 'password', 'nama_lengkap', 'nip_nis', 'instansi', 'kelas_id', 'jabatan_kelas'];
    foreach ($headers as $key => $header) {
        $sheet->setCellValue([$key + 1, 1], $header);
    }

    // Contoh Data
    $exampleData = [
        ['budi123', 'siswa', '123456', 'Budi Santoso', '1001', 'SMK N 1', '1', 'Ketua Kelas'],
        ['ani_guru', 'guru', '123456', 'Ani Wijaya', '198812...', 'SMK N 1', '', 'Guru Mapel'],
    ];

    foreach ($exampleData as $rowKey => $rowData) {
        foreach ($rowData as $colKey => $cellValue) {
            $sheet->setCellValue([$colKey + 1, $rowKey + 2], $cellValue);
        }
    }

    // Auto size columns
    foreach (range('A', 'H') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="template_import_user.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
}
