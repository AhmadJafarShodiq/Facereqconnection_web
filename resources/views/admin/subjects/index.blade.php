@extends('admin.layouts.app')
@section('title','Mapel')

@section('content')
<div class="card border-0 shadow-modern rounded-modern">
    <div class="card-header border-0 bg-transparent py-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-journal-bookmark text-primary me-2"></i>Mata Pelajaran
            </h5>
            <div class="d-flex gap-2">
                <form class="d-flex gap-2" method="GET">
                    <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden" style="width: 250px;">
                        <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}"
                               class="form-control border-0 ps-0"
                               placeholder="Cari mata pelajaran...">
                    </div>
                    <button class="btn btn-primary btn-sm px-3 shadow-sm rounded-3">Cari</button>
                </form>

                <a href="{{ route('admin.subjects.create') }}"
                   class="btn btn-success d-flex align-items-center shadow-sm rounded-3">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Mapel
                </a>
            </div>
        </div>
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
                        <th class="ps-4">Nama Mata Pelajaran</th>
                        <th class="pe-4 text-end" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $s)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-book-half"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $s->nama_mapel }}</span>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('admin.subjects.edit',$s->id) }}"
                               class="btn btn-sm btn-white text-warning shadow-sm border py-2 px-3 rounded-3" title="Edit">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x d-block mb-3 fs-1 opacity-25"></i>
                            Tidak ada mata pelajaran ditemukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
