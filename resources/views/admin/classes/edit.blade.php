@extends('admin.layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Kelas</label>
                <input type="text" name="nama_kelas"
                       value="{{ $class->nama_kelas }}"
                       class="form-control" required>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
