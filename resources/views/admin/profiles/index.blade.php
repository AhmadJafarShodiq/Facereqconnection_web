@extends('admin.layouts.app')
@section('title', 'Profiles')

@section('content')

<div class="row">
    <div class="col-12">

<div class="card border-0 shadow-modern rounded-modern">
    <div class="card-header border-0 bg-transparent py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-person-lines-fill text-primary me-2"></i>Data Profil User
            </h5>
            <div class="d-flex gap-2">
                <form method="GET" id="searchForm" class="d-flex gap-2 mb-0">
                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden" style="width: 250px;">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                               class="form-control border-0 ps-0" placeholder="Cari nama, username, atau NIP...">
                    </div>
                </form>
                <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm rounded-3">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Profil
                </a>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="px-4 pb-2">
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>NIP/NIS</th>
                        <th>Jabatan / Instansi</th>
                        <th>Kelas</th>
                        <th class="pe-4 text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profiles as $profile)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-1 me-2" style="width: 40px; height: 40px;">
                                    <div class="bg-info text-white rounded-circle w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person-circle small"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $profile->nama_lengkap }}</div>
                                    <small class="text-muted">Username: <code>{{ $profile->user->username ?? '-' }}</code></small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge border bg-white text-dark fw-bold">{{ $profile->nip_nis }}</span></td>
                        <td>
                            <div class="small fw-medium">{{ $profile->jabatan_kelas ?: '-' }}</div>
                            <div class="smaller text-muted">{{ $profile->instansi }}</div>
                        </td>
                        <td>
                            @if($profile->user && $profile->user->role === 'siswa' && $profile->kelas)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $profile->kelas->nama_kelas }}</span>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <a href="{{ route('admin.profiles.show', $profile->id) }}"
                                   class="btn btn-sm btn-white text-info py-2" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.profiles.edit', $profile->id) }}"
                                   class="btn btn-sm btn-white text-warning py-2" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x d-block mb-3 fs-1 opacity-25"></i>
                            Data profil tidak ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profiles->hasPages())
        <div class="mt-4 px-4 pb-4">
            {{ $profiles->links() }}
        </div>
        @endif
    </div>
</div>

    </div>
</div>

@endsection
