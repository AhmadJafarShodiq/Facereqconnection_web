@extends('admin.layouts.app')
@section('title','Jadwal')

@section('content')
<div class="card border-0 shadow-modern rounded-modern">
    <div class="card-header border-0 bg-transparent py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-calendar3 text-primary me-2"></i>Jadwal Pelajaran
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <form method="GET" class="d-flex gap-2">
                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden" style="width: 250px;">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}"
                               class="form-control border-0 ps-0"
                               placeholder="Cari mapel atau guru...">
                    </div>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm rounded-3">Cari</button>
                </form>

                <a href="{{ route('admin.schedules.create') }}"
                   class="btn btn-success d-flex align-items-center shadow-sm rounded-3">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Jadwal
                </a>
            </div>
        </div>
    </div>

    <div class="card-body p-4 pt-0">
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="p-3 bg-light rounded-3 border-dashed h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold small text-uppercase text-muted mb-0">Import dari Excel</h6>
                        <a href="{{ route('admin.schedules.template') }}" class="btn btn-link btn-sm text-decoration-none p-0">
                            <i class="bi bi-download me-1"></i> Download Template
                        </a>
                    </div>
                    <form action="{{ route('admin.schedules.import') }}" method="POST" enctype="multipart/form-data" class="row g-2">
                        @csrf
                        <div class="col">
                            <input type="file" name="file" class="form-control form-control-sm border-0 shadow-sm" accept=".xlsx,.xls" required>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-success btn-sm px-3 rounded-3">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-5 text-md-end d-flex align-items-end justify-content-md-end">
                <form action="{{ route('admin.schedules.deleteAll') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA jadwal?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm border-0">
                        <i class="bi bi-trash3 me-1"></i> Hapus Semua Jadwal
                    </button>
                </form>
            </div>
        </div>

        @if(session('import_errors'))
            <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-4">
                <h6 class="alert-heading fw-bold mb-2 small"><i class="bi bi-exclamation-triangle-fill me-2"></i>Beberapa baris dilewati:</h6>
                <ul class="mb-0 small ps-3">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="table-responsive rounded-3 overflow-hidden border">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Hari</th>
                        <th>Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Ruangan</th>
                        <th class="pe-4 text-end" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $s)
                    <tr>
                        <td class="ps-4">
                            <span class="badge @if(in_array($s->hari, ['Sabtu', 'Minggu'])) bg-danger-subtle text-danger @else bg-primary-subtle text-primary @endif px-3">
                                {{ $s->hari }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock text-muted me-2 small"></i>
                                <span class="fw-medium">{{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}</span>
                            </div>
                        </td>
                        <td><span class="fw-bold text-dark">{{ $s->subject->nama_mapel ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="bi bi-person text-secondary small"></i>
                                </div>
                                <span class="small">{{ $s->guru->profile->nama_lengkap ?? '-' }}</span>
                            </div>
                        </td>
                        <td><span class="badge border bg-light text-dark fw-normal">{{ $s->kelas->nama_kelas ?? '-' }}</span></td>
                        <td><span class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $s->ruangan ?? '-' }}</span></td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.schedules.edit',$s->id) }}"
                               class="btn btn-sm btn-light text-warning border shadow-sm py-2 px-3 rounded-3" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x d-block mb-3 fs-1 opacity-25"></i>
                            Jadwal belum tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
