@extends('admin.layouts.app')
@section('title','Attendance')

@section('content')
<div class="card border-0 shadow-modern rounded-modern mb-4">
    <div class="card-header border-0 bg-transparent py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-calendar-check text-primary me-2"></i>Rekap Absensi Harian
            </h5>
            <a href="{{ route('admin.attendance.export', request()->query()) }}"
               class="btn btn-danger d-flex align-items-center shadow-sm rounded-3 px-4 py-2">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="card-body p-4 pt-0">
        {{-- FORM FILTER --}}
        <div class="p-3 bg-light rounded-4 border-0 mb-4 shadow-sm">
            <form method="GET" id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Tanggal</label>
                    <div class="input-group overflow-hidden rounded-3 shadow-sm border-0">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-calendar-event text-primary"></i></span>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                               class="form-control border-0"
                               onchange="document.getElementById('filterForm').submit()">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase text-muted">Role</label>
                    <select name="role" class="form-select border-0 shadow-sm rounded-3"
                            onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Role</option>
                        <option value="guru" {{ request('role')=='guru'?'selected':'' }}>Guru</option>
                        <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>Siswa</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase text-muted">Cari User</label>
                    <select name="user_id" class="form-select border-0 shadow-sm rounded-3"
                            onchange="document.getElementById('filterForm').submit()">
                        <option value="">Cari Username / Nama...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id')==$user->id?'selected':'' }}>
                                {{ $user->username }} - {{ $user->profile->nama_lengkap ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="button" onclick="window.location.href='{{ route('admin.attendance.index') }}'"
                            class="btn btn-outline-secondary border-0 shadow-sm rounded-3 py-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </form>
        </div>

        {{-- TABEL --}}
        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">User & Role</th>
                        <th>Mapel & Kelas</th>
                        <th>Waktu (Absen)</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th class="pe-4 text-end" width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $a)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-1 me-2" style="width: 40px; height: 40px;">
                                    <div class="bg-{{ $a->role == 'guru' ? 'primary' : 'success' }} text-white rounded-circle w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person{{ $a->role == 'guru' ? '-workspace' : '' }} small"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $a->user->profile->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted d-block">UID: {{ $a->user->username }} | <span class="badge bg-light text-dark fw-normal border p-0 px-1">{{ ucfirst($a->role) }}</span></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $a->subject->nama_mapel ?? '-' }}</div>
                            <span class="badge bg-light text-muted border fw-normal">{{ $a->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="small"><i class="bi bi-box-arrow-in-right text-success me-1"></i>{{ optional($a->jam_masuk)->format('H:i') ?? '--:--' }}</span>
                                <span class="small"><i class="bi bi-box-arrow-right text-danger me-1"></i>{{ optional($a->jam_pulang)->format('H:i') ?? '--:--' }}</span>
                                <small class="text-muted mt-1">{{ $a->tanggal->format('d/m/Y') }}</small>
                            </div>
                        </td>
                        <td>
                            @if($a->status == 'hadir')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 fw-bold">HADIR</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 fw-bold">{{ strtoupper($a->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($a->foto)
                                <div class="position-relative d-inline-block hover-zoom">
                                    <a href="{{ asset('storage/'.$a->foto) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$a->foto) }}" class="rounded shadow-sm border" style="width: 45px; height: 45px; object-fit: cover;">
                                    </a>
                                </div>
                            @else
                                <span class="text-muted small">No Photo</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.attendance.show',$a->id) }}"
                               class="btn btn-sm btn-light text-info border shadow-sm py-2 px-3 rounded-3" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-search d-block mb-3 fs-1 opacity-25"></i>
                            Tidak ada data absensi ditemukan untuk filter ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
