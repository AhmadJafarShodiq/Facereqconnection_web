@extends('admin.layouts.app')
@section('title','Tambah User')

@section('content')
<h3>Import Excel User</h3>
<form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>File Excel (.xlsx)</label>
        <input type="file" 
               name="file" 
               class="form-control" 
               accept=".xlsx,.xls" 
               required>
        <small class="text-muted">
            Format kolom: username | role | password | nama_lengkap | nip_nis | kelas_id
        </small>
    </div>
    <button class="btn btn-primary">
        <i class="bi bi-upload"></i> Import Excel
    </button>
</form>

<hr>

<h2>Tambah User</h2>
<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
      <select name="role" class="form-control" required>
    <option value="admin">Admin</option>
    <option value="guru">Guru</option>
    <option value="siswa">Siswa</option>
</select>

    </div>
    <button class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
</form>
@endsection
