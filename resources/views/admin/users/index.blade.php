@extends('admin.layouts.app')

@section('title', 'Master User')

@section('content')

<div class="card border-0 shadow-modern rounded-modern">
    <div class="card-header border-0 bg-transparent py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-people text-primary me-2"></i>Master User
            </h5>
            <div class="d-flex gap-2">
                <form method="GET" id="filterForm" class="d-flex gap-2">
                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden" style="width: 200px;">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control border-0 ps-0" placeholder="Cari user...">
                    </div>
                    <select name="role" class="form-select form-select-sm border-0 shadow-sm rounded-3"
                            style="width: 130px;" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                        <option value="guru" {{ request('role')=='guru'?'selected':'' }}>Guru</option>
                        <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>Siswa</option>
                    </select>
                </form>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-flex align-items-center shadow-sm rounded-3">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah User
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

        @if(session('import_errors'))
            <div class="px-4 pb-2">
                <div class="alert alert-warning border-0 shadow-sm rounded-3">
                    <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Beberapa baris dilewati:</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Lengkap</th>
                        <th>Credentials</th>
                        <th>Role</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="pe-4 text-end" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-1 me-2" style="width: 38px; height: 38px;">
                                    <div class="bg-primary text-white rounded-circle w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person small"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->profile?->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $user->profile?->nip_nis }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-medium text-dark">{{ $user->username }}</div>
                            <div class="text-muted smaller">ID: #{{ $user->id }}</div>
                        </td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger text-white">Admin</span>
                            @elseif($user->role === 'guru')
                                <span class="badge bg-primary">Guru</span>
                            @elseif($user->role === 'siswa')
                                <span class="badge bg-success">Siswa</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role === 'siswa' && $user->profile && $user->profile->kelas)
                                <span class="badge bg-light text-dark border fw-normal">{{ $user->profile->kelas->nama_kelas }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                    <i class="bi bi-x-circle me-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <a href="{{ route('admin.users.show',$user->id) }}"
                                   class="btn btn-sm btn-white text-info py-2" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit',$user->id) }}"
                                   class="btn btn-sm btn-white text-warning py-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.toggle',$user->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-white {{ $user->is_active?'text-danger':'text-success' }} py-2" 
                                            title="{{ $user->is_active?'Nonaktifkan':'Aktifkan' }}">
                                        <i class="bi {{ $user->is_active?'bi-slash-circle':'bi-play-circle' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x d-block mb-3 fs-1 opacity-25"></i>
                            Tidak ada user ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="mt-4 px-4 pb-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

    </div>
</div>

@endsection
