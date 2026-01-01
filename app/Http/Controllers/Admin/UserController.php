<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // LIST USER
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
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
            'role'     => 'required|in:admin,user'
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
            'role'     => 'required|in:admin,user'
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
        return view('admin.users.show', compact('user'));
    }
}
