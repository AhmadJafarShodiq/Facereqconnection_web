@extends('admin.layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.classes.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas" class="form-control" required>
            </div>

            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
