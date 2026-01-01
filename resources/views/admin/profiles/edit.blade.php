@extends('admin.layouts.app')
@section('title','Edit Profile')

@section('content')
<h2>Edit Profile</h2>
<form action="{{ route('admin.profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="{{ $profile->nama_lengkap }}" required>
    </div>
    <div class="mb-3">
        <label>NIP/NIS</label>
        <input type="text" name="nip_nis" class="form-control" value="{{ $profile->nip_nis }}" required>
    </div>
    <div class="mb-3">
        <label>Jabatan/Kelas</label>
        <input type="text" name="jabatan_kelas" class="form-control" value="{{ $profile->jabatan_kelas }}" required>
    </div>
    <div class="mb-3">
        <label>Instansi</label>
        <input type="text" name="instansi" class="form-control" value="{{ $profile->instansi }}" required>
    </div>
    <div class="mb-3">
        <label>Foto</label>
        <input type="file" name="foto" class="form-control">
        @if($profile->foto)
            <img src="{{ asset('storage/'.$profile->foto) }}" width="80" class="mt-2">
        @endif
    </div>
    <button class="btn btn-success"><i class="bi bi-save"></i> Update</button>
</form>
@endsection
