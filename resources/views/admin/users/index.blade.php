@extends('admin.layouts.app')

@section('title', 'Master User')

@section('content')
<h2 class="mb-4">List Users</h2>
<a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3"><i class="bi bi-plus"></i> Tambah User</a>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Aktif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->is_active ? 'Ya' : 'Tidak' }}</td>
            <td>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Detail</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
