<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Kelas::latest()->paginate(10);
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100'
        ]);

        Kelas::create($request->only('nama_kelas'));

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit(Kelas $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    public function update(Request $request, Kelas $class)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:100'
        ]);

        $class->update($request->only('nama_kelas'));

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diupdate');
    }

    public function destroy(Kelas $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus');
    }
}
