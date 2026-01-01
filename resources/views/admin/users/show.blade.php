@extends('admin.layouts.app')
@section('title','Detail User')

@section('content')
<h2>Detail User</h2>
<ul class="list-group">
    <li class="list-group-item"><strong>Username:</strong> {{ $user->username }}</li>
    <li class="list-group-item"><strong>Role:</strong> {{ $user->role }}</li>
    <li class="list-group-item"><strong>Aktif:</strong> {{ $user->is_active?'Ya':'Tidak' }}</li>
</ul>
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-2"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
