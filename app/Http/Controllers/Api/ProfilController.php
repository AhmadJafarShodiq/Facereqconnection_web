<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class ProfilController extends Controller
{

    public function profile(Request $request)
    {
        $user = $request->user()->load('profile.kelas');

        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }


    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nip_nis' => 'required|string|max:50',
            'jabatan_kelas' => 'nullable|string|max:100',
            'instansi' => 'nullable|string|max:255',
        ]);

        $profile = $request->user()->profile;

        if(!$profile){
            return response()->json([
                'status' => false,
                'message' => 'Profil tidak ditemukan'
            ],404);
        }

        $profile->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nip_nis' => $request->nip_nis,
            'jabatan_kelas' => $request->jabatan_kelas,
            'instansi' => $request->instansi,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $profile
        ]);
    }


  public function updateFoto(Request $request)
{
    $request->validate([
        'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $user = $request->user();
    $profile = $user->profile;

    if (!$profile) {
        return response()->json([
            'status' => false,
            'message' => 'Profil tidak ditemukan'
        ], 404);
    }

    if ($request->hasFile('foto')) {

        $file = $request->file('foto');
        $namaFile = time() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('foto_profile'), $namaFile);

        $profile->foto = $namaFile;
        $profile->save();
    }

    return response()->json([
        "status" => true,
        "message" => "Foto berhasil diupload",
        "foto" => url('foto_profile/' . $profile->foto)
    ]);
}

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {

            return response()->json([
                'status' => false,
                'message' => 'Password lama salah'
            ],401);
        }

        $user->password = Hash::make($request->new_password);

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }
}