@extends('admin.layouts.app')
@section('title', 'Profiles')

@section('content')
    <h2 class="mb-4">List Profiles</h2>
    <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary mb-3"><i class="bi bi-plus"></i> Tambah
        Profile</a>
    @session('success')
        <div class="alert alert-success">
            {{session("success")}}
        </div>
    @endsession
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Nama</th>
                <th>NIP/NIS</th>
                <th>Jabatan/Kelas</th>
                <th>Instansi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profiles as $profile)
                <tr>
                    <td>{{ $profile->nama_lengkap }}</td>
                    <td>{{ $profile->nip_nis }}</td>
                    <td>{{ $profile->jabatan_kelas }}</td>
                    <td>{{ $profile->instansi }}</td>
                    <td>
                        <a href="{{ route('admin.profiles.edit', $profile->id) }}" class="btn btn-sm btn-warning"><i
                                class="bi bi-pencil"></i> Edit</a>
                        <a href="{{ route('admin.profiles.show', $profile->id) }}" class="btn btn-sm btn-info"><i
                                class="bi bi-eye"></i> Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
