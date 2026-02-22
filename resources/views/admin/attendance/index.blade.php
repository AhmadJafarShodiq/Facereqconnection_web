@extends('admin.layouts.app')
@section('title','Attendance')

@section('content')
<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Rekap Absensi</h5>

        {{-- FORM FILTER --}}
       <form method="GET" id="filterForm" class="row g-2 mb-3">

    {{-- Filter Tanggal --}}
    <div class="col-md-3">
        <input type="date"
               name="tanggal"
               value="{{ request('tanggal') }}"
               class="form-control"
               onchange="document.getElementById('filterForm').submit()">
    </div>

    {{-- Filter Role --}}
    <div class="col-md-3">
        <select name="role"
                class="form-select"
                onchange="document.getElementById('filterForm').submit()">

            <option value="">-- Semua Role --</option>
            <option value="guru" {{ request('role')=='guru'?'selected':'' }}>
                Guru
            </option>
            <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>
                Siswa
            </option>
        </select>
    </div>

    {{-- Filter User --}}
    <div class="col-md-3">
        <select name="user_id"
                class="form-select"
                onchange="document.getElementById('filterForm').submit()">

            <option value="">-- Semua User --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    {{ request('user_id')==$user->id?'selected':'' }}>
                    {{ $user->username }}
                </option>
            @endforeach
        </select>
    </div>

</form>

        {{-- TABEL --}}
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Role</th>
                    <th>User</th>
                    <th>Nama</th>
                    <th>Mapel</th>
                    <th>Kelas</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                    <th>Foto</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $a)
                <tr>
                    <td>{{ $a->tanggal->format('Y-m-d') }}</td>

                    {{-- Role --}}
                    <td>
                        <span class="badge bg-secondary">
                            {{ ucfirst($a->role) }}
                        </span>
                    </td>

                    <td>{{ $a->user->username }}</td>
                    <td>{{ $a->user->profile->nama_lengkap ?? '-' }}</td>
                    <td>{{ $a->subject->nama_mapel ?? '-' }}</td>
                    <td>{{ $a->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ optional($a->jam_masuk)->format('H:i') ?? '-' }}</td>
                    <td>{{ optional($a->jam_pulang)->format('H:i') ?? '-' }}</td>

                    <td>
                        <span class="badge bg-{{ $a->status=='hadir'?'success':'danger' }}">
                            {{ strtoupper($a->status) }}
                        </span>
                    </td>

                    <td>
                        @if($a->foto)
                            <a href="{{ asset('storage/'.$a->foto) }}" target="_blank">
                                <img src="{{ asset('storage/'.$a->foto) }}" width="50">
                            </a>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.attendance.show',$a->id) }}"
                           class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center">Data kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $attendances->links() }}

        <a href="{{ route('admin.attendance.export', request()->query()) }}"
           class="btn btn-success mt-3">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>

    </div>
</div>
@endsection
