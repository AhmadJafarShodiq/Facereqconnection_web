<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::with('user')->get();
        return view('admin.profiles.index', compact('profiles'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.profiles.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|unique:profiles,user_id',
            'nama_lengkap' => 'required',
            'nip_nis' => 'required',
            'jabatan_kelas' => 'required',
            'instansi' => 'required',
            'foto' => 'nullable|image'
        ]);

        $path = $request->file('foto') ? $request->file('foto')->store('profiles','public') : null;

        Profile::create([
            'user_id' => $request->user_id,
            'nama_lengkap' => $request->nama_lengkap,
            'nip_nis' => $request->nip_nis,
            'jabatan_kelas' => $request->jabatan_kelas,
            'instansi' => $request->instansi,
            'foto' => $path
        ]);

        return redirect()->route('admin.profiles.index')->with('success','Profile berhasil ditambahkan');
    }

    public function edit(Profile $profile)
    {
        return view('admin.profiles.edit', compact('profile'));
    }

    public function update(Request $request, Profile $profile)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'nip_nis' => 'required',
            'jabatan_kelas' => 'required',
            'instansi' => 'required',
            'foto' => 'nullable|image'
        ]);

        if($request->hasFile('foto')){
            if($profile->foto){
                Storage::disk('public')->delete($profile->foto);
            }
            $profile->foto = $request->file('foto')->store('profiles','public');
        }

        $profile->update([
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
