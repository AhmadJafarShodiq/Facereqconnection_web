@extends('admin.layouts.app')
@section('title','Edit User')

@section('content')
<h2>Edit User</h2>
<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-control" required>
        <option value="guru" {{ $user->role=='guru'?'selected':'' }}>Guru</option>
        <option value="siswa" {{ $user->role=='siswa'?'selected':'' }}>Siswa</option>

        </select>
    </div>
    <button class="btn btn-success"><i class="bi bi-save"></i> Update</button>
</form>
@endsection
