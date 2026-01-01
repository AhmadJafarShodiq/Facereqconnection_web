@extends('admin.layouts.app')
@section('title','Tambah Profile')

@section('content')
<h2>Tambah Profile</h2>
<form action="{{ route('admin.profiles.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>User</label>
        <select name="user_id" class="form-control" required>
            @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->username }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>NIP/NIS</label>
        <input type="text" name="nip_nis" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Jabatan/Kelas</label>
        <input type="text" name="jabatan_kelas" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Instansi</label>
        <input type="text" name="instansi" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="foto" class="form-control">
    </div>
    <button class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
</form>
@endsection
