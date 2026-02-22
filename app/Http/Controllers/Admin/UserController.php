<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        ->orderBy('id','desc')
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
        'file' => 'required|mimes:csv,txt'
    ]);

    $path = $request->file('file')->getRealPath();
    $file = fopen($path, 'r');

    $header = fgetcsv($file);

    $inserted = 0;
    $skipped  = 0;

    while ($row = fgetcsv($file)) {
        $data = array_combine($header, $row);

        // skip duplicate username
        if(User::where('username', $data['username'])->exists()){
            $skipped++;
            continue;
        }

        // buat user
        $user = User::create([
            'username' => $data['username'],
            'password' => isset($data['password']) && $data['password'] != '' ? Hash::make($data['password']) : Hash::make('123456'),
            'role'     => $data['role'],
            'is_active'=> 1
        ]);

        // buat profile termasuk jabatan
      $user->profile()->create([
    'nama_lengkap' => $data['nama_lengkap'] ?? null,
    'nip_nis'      => $data['nip_nis'] ?? null,
    'instansi'     => $data['instansi'] ?? null,
    'kelas_id'     => !empty($data['kelas_id']) ? $data['kelas_id'] : null,
    'jabatan_kelas'      => $data[''] ?? null,
]);


        $inserted++;
    }

    fclose($file);

    return redirect()->route('admin.users.index')
        ->with('success', "Import selesai. $inserted user ditambahkan, $skipped user dilewati karena username sudah ada.");
}



}
