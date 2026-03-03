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
    $spreadsheet = IOFactory::load($file->getRealPath());
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $inserted = 0;
    $skipped  = 0;

    foreach ($rows as $index => $row) {

        if ($index == 0) continue; // skip header

        $username       = trim($row[0] ?? '');
        $role           = trim($row[1] ?? '');
        $password       = trim($row[2] ?? '123456');
        $nama_lengkap   = trim($row[3] ?? '');
        $nip_nis        = trim($row[4] ?? '');
        $kelas_id       = $row[5] ?? null;
        $jabatan_kelas  = trim($row[6] ?? '');

        if (!$username || !$role) {
            $skipped++;
            continue;
        }

        if (!in_array($role, ['admin','guru','siswa'])) {
            $skipped++;
            continue;
        }

        if (User::where('username', $username)->exists()) {
            $skipped++;
            continue;
        }

        $user = User::create([
            'username'  => $username,
            'password'  => Hash::make($password ?: '123456'),
            'role'      => $role,
            'is_active' => 1
        ]);

        $user->profile()->create([
            'nama_lengkap'  => $nama_lengkap ?: null,
            'nip_nis'       => $nip_nis ?: null,
            'kelas_id'      => $kelas_id ?: null,
            'jabatan_kelas' => $jabatan_kelas ?: null,
        ]);

        $inserted++;
    }

    return redirect()->route('admin.users.index')
        ->with('success', "Import selesai. $inserted user ditambahkan, $skipped dilewati.");
}




public function export()
{
    $filename = "users_" . date('Ymd_His') . ".csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $users = User::with('profile.kelas')->orderBy('id','asc')->get();

    $callback = function() use ($users) {
        $file = fopen('php://output', 'w');

        // Header kolom
        fputcsv($file, [
            'ID',
            'Username',
            'Role',
            'Nama Lengkap',
            'NIP/NIS',
            'Kelas',
            'Status'
        ]);

        foreach ($users as $user) {
            fputcsv($file, [
                $user->id,
                $user->username,
                $user->role,
                optional($user->profile)->nama_lengkap,
                optional($user->profile)->nip_nis,
                optional(optional($user->profile)->kelas)->nama,
                $user->is_active ? 'Aktif' : 'Nonaktif'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
