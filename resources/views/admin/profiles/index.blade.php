@extends('admin.layouts.app')
@section('title', 'Profiles')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm">

            <div class="card-header">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <h3 class="card-title mb-0">List Profiles</h3>

        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2 mb-0">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       class="form-control form-control-sm"
                       placeholder="Cari nama / username / nip...">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.profiles.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus"></i> Tambah Profile
            </a>
        </div>
    </div>
</div>


            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" class="d-flex gap-2 mb-3">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           class="form-control"
                           placeholder="Cari nama / username / nip...">

                    <button class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>NIP/NIS</th>
                                <th>Jabatan</th>
                                <th>Instansi</th>
                                <th>Kelas</th>
                                <th width="160">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($profiles as $profile)
                                <tr>
                                    <td>{{ $profile->user->username ?? '-' }}</td>
                                    <td>{{ $profile->nama_lengkap }}</td>
                                    <td>{{ $profile->nip_nis }}</td>
                                    <td>{{ $profile->jabatan_kelas }}</td>
                                    <td>{{ $profile->instansi }}</td>
                                    <td>
                                        @if($profile->user && $profile->user->role === 'siswa' && $profile->kelas)
                                            {{ $profile->kelas->nama_kelas }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.profiles.edit', $profile->id) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <a href="{{ route('admin.profiles.show', $profile->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $profiles->links() }}
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
