@extends('admin.layouts.app')
@section('title', 'Face Data')

@section('content')
<div class="face-user-container">
    <div class="card border-0 shadow-modern rounded-modern">
        <div class="card-header border-0 bg-transparent py-4">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-person-bounding-box text-primary me-2"></i>Registrasi Wajah User
        </h5>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="px-4 pb-3">
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
                        <th class="ps-4">Username</th>
                        <th>Nama Lengkap</th>
                        <th>Status Wajah</th>
                        <th class="pe-4 text-end" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4"><code>{{ $user->username }}</code></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $user->profile->nama_lengkap ?? '-' }}</div>
                            <small class="text-muted">{{ ucfirst($user->role) }}</small>
                        </td>
                        <td>
                            @if($user->faceData)
                                <span class="badge bg-success shadow-sm">
                                    <i class="bi bi-shield-check me-1"></i> Terdaftar
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-normal">
                                    <i class="bi bi-shield-x me-1"></i> Belum Ada
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            @if($user->faceData)
                                <form method="POST"
                                      action="{{ route('admin.face-data.reset',$user->id) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data wajah user ini? User harus melakukan scan ulang.')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger shadow-sm px-3 rounded-3">
                                        <i class="bi bi-trash3 me-1"></i> Reset Wajah
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
@endsection
