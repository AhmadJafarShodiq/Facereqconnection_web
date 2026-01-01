<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FaceData;

class FaceDataController extends Controller
{
    public function index()
    {
        $users = User::with('profile','faceData')->get();
        return view('admin.face-data.index', compact('users'));
    }

    public function reset(User $user)
    {
        if($user->faceData){
            $user->faceData()->delete();
        }
        return redirect()->route('admin.face-data.index')->with('success','Face data berhasil direset');
    }
}
