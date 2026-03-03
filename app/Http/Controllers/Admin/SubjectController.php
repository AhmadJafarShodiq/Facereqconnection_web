<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Kelas;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $subjects = Subject::with('kelas')
            ->when($search, fn($q) =>
                $q->where('nama_mapel','like',"%{$search}%")
            )
            ->paginate(10)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects','search'));
    }

   public function create()
{
    return view('admin.subjects.create', [
        'kelas' => Kelas::all()
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
    'nama_mapel' => 'required|string',
]);

$data['kelas_id'] = Kelas::first()->id; // otomatis ambil kelas pertama

        Subject::create($data);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success','Mapel berhasil ditambahkan');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', [
            'subject' => $subject,
            'kelas'   => Kelas::all()
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
    'nama_mapel' => 'required|string',
]);

// tidak ubah kelas_id saat update

        $subject->update($data);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success','Mapel berhasil diupdate');
    }
}
