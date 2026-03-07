@extends('admin.layouts.app')

@section('title','Dashboard')

@section('content')

<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-primary shadow-modern">
            <div class="stat-content">
                <span class="stat-title fw-bold">Total Guru</span>
                <h2>{{ $totalGuru }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-person-video3"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-success shadow-modern">
            <div class="stat-content">
                <span class="stat-title fw-bold">Total Siswa</span>
                <h2>{{ $totalSiswa }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-warning shadow-modern text-dark">
            <div class="stat-content text-white">
                <span class="stat-title fw-bold">Total Kelas</span>
                <h2 class="text-white">{{ $totalKelas }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-door-open-fill text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-danger shadow-modern">
            <div class="stat-content">
                <span class="stat-title fw-bold">Total Mapel</span>
                <h2>{{ $totalMapel }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ================= SESI AKTIF ================= --}}
    <div class="col-xl-7 col-lg-6">
        <div class="card border-0 shadow-modern rounded-modern">
            <div class="card-header border-0 bg-transparent py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-broadcast text-primary me-2"></i>Kelas Berlangsung
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 rounded-pill">{{ $sesiAktif }} Aktif</span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Guru</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th class="pe-4 text-end">Waktu Mulai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelasSedangBerlangsung as $sesi)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>{{ $sesi->guru->profile->nama_lengkap ?? '-' }}</div>
                                    </div>
                                </td>
                                <td><span class="fw-medium">{{ $sesi->subject->nama_mapel ?? '-' }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $sesi->kelas->nama_kelas ?? '-' }}</span></td>
                                <td class="pe-4 text-end text-muted small">{{ $sesi->started_at?->format('H:i') }} <br> <span class="very-small">{{ $sesi->started_at?->format('d M') }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history d-block mb-2 fs-2 opacity-25"></i>
                                    Tidak ada sesi aktif saat ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= LOG AKTIVITAS ================= --}}
    <div class="col-xl-5 col-lg-6">
        <div class="card border-0 shadow-modern rounded-modern">
            <div class="card-header border-0 bg-transparent py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-clock-history text-success me-2"></i>Aktivitas Terakhir
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($logAktivitas as $log)
                    <div class="list-group-item border-0 py-3 px-4 @if(!$loop->last) border-bottom @endif">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                @if($log->status == 'hadir')
                                    <div class="bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-check2-circle fs-4"></i>
                                    </div>
                                @elseif($log->status == 'pulang')
                                    <div class="bg-secondary-subtle text-secondary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-box-arrow-right fs-4"></i>
                                    </div>
                                @else
                                    <div class="bg-warning-subtle text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-exclamation-triangle fs-4"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $log->user->profile->nama_lengkap ?? '-' }}</h6>
                                        <small class="text-muted">{{ ucfirst($log->status) }} @ {{ $log->created_at?->format('H:i') }}</small>
                                    </div>
                                    <small class="text-muted small-timestamp">{{ $log->created_at?->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-5 text-center text-muted">
                        Belum ada aktivitas hari ini
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 text-center pb-3">
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-link btn-sm text-decoration-none text-primary fw-bold">Lihat Semua Riwayat <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection
