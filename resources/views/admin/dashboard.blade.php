@extends('admin.layouts.app')

@section('title','Dashboard')

@section('content')

<div class="row mb-4">

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-primary">
            <div class="stat-content">
                <span class="stat-title">Total Guru</span>
                <h2>{{ $totalGuru }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-success">
            <div class="stat-content">
                <span class="stat-title">Total Siswa</span>
                <h2>{{ $totalSiswa }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-warning">
            <div class="stat-content">
                <span class="stat-title">Total Kelas</span>
                <h2>{{ $totalKelas }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-building"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stat-card bg-danger">
            <div class="stat-content">
                <span class="stat-title">Total Mapel</span>
                <h2>{{ $totalMapel }}</h2>
            </div>
            <div class="stat-icon">
                <i class="bi bi-book"></i>
            </div>
        </div>
    </div>

</div>


<div class="row mt-4">

    {{-- ================= SESI AKTIF ================= --}}

    <div class="col-md-6">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">
                    Kelas Sedang Berlangsung 
                    <span class="badge bg-primary">{{ $sesiAktif }}</span>
                </h5>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Guru</th>
                            <th>Mapel</th>
                            <th>Kelas</th>
                            <th>Mulai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelasSedangBerlangsung as $sesi)
                        <tr>
                            <td>{{ $sesi->guru->profile->nama_lengkap ?? '-' }}</td>
                            <td>{{ $sesi->subject->nama_mapel ?? '-' }}</td>
                            <td>{{ $sesi->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $sesi->started_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Tidak ada sesi aktif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= LOG AKTIVITAS ================= --}}

    <div class="col-md-6">
        <div class="card shadow-sm rounded-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Aktivitas Terakhir</h5>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logAktivitas as $log)
                        <tr>
                            <td>{{ $log->user->profile->nama_lengkap ?? '-' }}</td>

                            <td>
                                @if($log->status == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($log->status == 'pulang')
                                    <span class="badge bg-secondary">Pulang</span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                @endif
                            </td>

                            <td>{{ $log->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Belum ada data
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection
