@extends('admin.layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">Data Kelas</h5>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm ms-auto">
            <i class="bi bi-plus me-1"></i> Tambah
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Kelas</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->nama_kelas }}</td>
                        <td>
                            <a href="{{ route('admin.classes.edit', $item->id) }}"
                               class="btn btn-warning btn-sm">
                               <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('admin.classes.destroy', $item->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Hapus data?')"
                                        class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Data kosong</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $classes->links() }}

    </div>
</div>
@endsection
