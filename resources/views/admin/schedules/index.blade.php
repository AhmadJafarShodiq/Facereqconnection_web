@extends('admin.layouts.app')
@section('title','Jadwal')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" class="d-flex gap-2 mb-0">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control form-control-sm"
                       placeholder="Cari mapel...">
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.schedules.create') }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-plus"></i> Tambah Jadwal
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                <tr>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Mapel</th>
                    <th>Guru</th>
                    <th>Kelas</th>
                    <th>Ruangan</th>
                    <th width="120">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($schedules as $s)
                    <tr>
                        <td>{{ $s->hari }}</td>
                        <td>{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
                        <td>{{ $s->subject->nama_mapel ?? '-' }}</td>
                        <td>{{ $s->guru->profile->nama_lengkap ?? '-' }}</td>
                        <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $s->ruangan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.schedules.edit',$s->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Data kosong</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $schedules->links() }}
    </div>
</div>
@endsection
