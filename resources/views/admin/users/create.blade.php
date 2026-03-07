@extends('admin.layouts.app')
@section('title','Tambah User')

@section('content')
<div class="card bg-light mb-3">
    <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-file-earmark-excel"></i> Import User via Excel</h5>
                <a href="{{ route('admin.users.template') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download"></i> Download Template (.xlsx)
                </a>
            </div>
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                <div class="form-text mt-2">
                    <strong>Format Kolom:</strong><br>
                    <code>username | role | password | nama_lengkap | nip_nis | instansi | kelas_id | jabatan_kelas</code>
                    <ul class="mb-0 mt-2 small">
                        <li><strong>Role:</strong> admin, guru, atau siswa (tidak sensitif huruf besar/kecil)</li>
                        <li><strong>Instansi:</strong> Nama sekolah/kantor (opsional)</li>
                        <li><strong>Kelas ID:</strong> Hanya untuk siswa (gunakan ID angka dari menu <a href="{{ route('admin.classes.index') }}" target="_blank">Data Kelas</a>)</li>
                        <li><strong>Password:</strong> Jika kosong, default adalah <code>123456</code></li>
                    </ul>
                </div>
            </div>
            <button class="btn btn-primary">
                <i class="bi bi-upload"></i> Mulai Import
            </button>
        </form>
    </div>
</div>

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
