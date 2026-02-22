<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
  public function index(Request $request)
{
    $search = $request->search;

    $profiles = Profile::with(['user','kelas'])
        ->when($search, function ($query) use ($search) {
            $query->where('nama_lengkap', 'like', "%$search%")
                  ->orWhere('nip_nis', 'like', "%$search%")
                  ->orWhere('jabatan_kelas', 'like', "%$search%")
                  ->orWhere('instansi', 'like', "%$search%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('username', 'like', "%$search%");
                  });
        })
        ->orderBy('id','desc')
        ->paginate(10)
        ->withQueryString();

    return view('admin.profiles.index', compact('profiles','search'));
}


   public function create()
{
    $users = User::doesntHave('profile')->get(); // biar tidak double profile
    $kelas = Kelas::all();

    return view('admin.profiles.create', compact('users','kelas'));
}


    public function store(Request $request)
    {
        $request->validate([
    'user_id' => 'required|unique:profiles,user_id',
    'kelas_id' => 'nullable|exists:kelas,id',
    'nama_lengkap' => 'required',
    'nip_nis' => 'required',
    'jabatan_kelas' => 'required',
    'instansi' => 'required',
    'foto' => 'nullable|image'
]);


        $path = $request->file('foto') ? $request->file('foto')->store('profiles','public') : null;

       Profile::create([
    'user_id' => $request->user_id,
    'kelas_id' => $request->kelas_id,
    'nama_lengkap' => $request->nama_lengkap,
    'nip_nis' => $request->nip_nis,
    'jabatan_kelas' => $request->jabatan_kelas,
    'instansi' => $request->instansi,
    'foto' => $path
]);


        return redirect()->route('admin.profiles.index')->with('success','Profile berhasil ditambahkan');
    }

    public function edit(Profile $profile)
    { $kelas = Kelas::all();
    return view('admin.profiles.edit', compact('profile','kelas'));
    }

    public function update(Request $request, Profile $profile)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'nip_nis' => 'required',
            'jabatan_kelas' => 'required',
            'instansi' => 'required',
            'foto' => 'nullable|image',
            'kelas_id' => 'nullable|exists:kelas,id',

        ]);

        if($request->hasFile('foto')){
            if($profile->foto){
                Storage::disk('public')->delete($profile->foto);
            }
            $profile->foto = $request->file('foto')->store('profiles','public');
        }

        $profile->update([
    'kelas_id' => $request->kelas_id,
    'nama_lengkap' => $request->nama_lengkap,
    'nip_nis' => $request->nip_nis,
    'jabatan_kelas' => $request->jabatan_kelas,
    'instansi' => $request->instansi,
    'foto' => $profile->foto
]);


        return redirect()->route('admin.profiles.index')->with('success','Profile berhasil diupdate');
    }

    public function show(Profile $profile)
    {
        return view('admin.profiles.show', compact('profile'));
    }
}
