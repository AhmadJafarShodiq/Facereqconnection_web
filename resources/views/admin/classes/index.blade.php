@extends('admin.layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="card border-0 shadow-modern rounded-modern">
    <div class="card-header border-0 bg-transparent py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-door-open text-primary me-2"></i>Data Kelas
        </h5>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> Tambah Kelas
        </a>
    </div>

    <div class="card-body p-0">
        @if(session('success'))
            <div class="px-4 pt-3">
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
                        <th class="ps-4" width="80">No</th>
                        <th>Nama Kelas</th>
                        <th class="pe-4 text-end" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $item)
                    <tr>
                        <td class="ps-4"><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-door-closed fs-5"></i>
                                </div>
                                <span class="fw-bold text-dark fs-6">{{ $item->nama_kelas }}</span>
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <a href="{{ route('admin.classes.edit', $item->id) }}"
                                   class="btn btn-sm btn-white text-warning py-2" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.classes.destroy', $item->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Hapus data kelas ini?')"
                                            class="btn btn-sm btn-white text-danger py-2" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x d-block mb-3 fs-1 opacity-25"></i>
                            Belum ada data kelas yang ditambahkan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $classes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
