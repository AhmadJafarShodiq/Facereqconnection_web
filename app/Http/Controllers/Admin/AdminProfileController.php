<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.settings.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
        ]);

        $user->update([
            'username' => $request->username
        ]);

        return back()->with('success', 'Profile updated successfully');
    }

    public function password()
    {
        return view('admin.settings.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password changed successfully');
    }
}
