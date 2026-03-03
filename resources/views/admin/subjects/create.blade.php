@extends('admin.layouts.app')
@section('title','Tambah Mapel')

@section('content')
<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('admin.subjects.store') }}">
            @csrf

            <div class="mb-3">
                <label>Nama Mapel</label>
                <input type="text" name="nama_mapel"
                       class="form-control" required>
            </div>

            <input type="hidden" name="kelas_id" value="{{ $kelas->first()->id }}">

            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.subjects.index') }}"
               class="btn btn-secondary">Batal</a>
        </form>

    </div>
</div>
@endsection
