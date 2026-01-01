@extends('admin.layouts.app')
@section('title','Detail Profile')

@section('content')
<h2>Detail Profile</h2>
<ul class="list-group">
    <li class="list-group-item"><strong>Username:</strong> {{ $profile->user->username }}</li>
    <li class="list-group-item"><strong>Nama:</strong> {{ $profile->nama_lengkap }}</li>
    <li class="list-group-item"><strong>NIP/NIS:</strong> {{ $profile->nip_nis }}</li>
    <li class="list-group-item"><strong>Jabatan/Kelas:</strong> {{ $profile->jabatan_kelas }}</li>
    <li class="list-group-item"><strong>Instansi:</strong> {{ $profile->instansi }}</li>
    <li class="list-group-item">
        <strong>Foto:</strong>
        @if($profile->foto)
            <img src="{{ asset('storage/'.$profile->foto) }}" width="100">
        @endif
    </li>
</ul>
<a href="{{ route('admin.profiles.index') }}" class="btn btn-secondary mt-2"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
