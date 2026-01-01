@extends('admin.layouts.app')
@section('title','Tambah User')

@section('content')
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
            <option value="user">User</option>
        </select>
    </div>
    <button class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
</form>
@endsection
