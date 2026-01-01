@extends('admin.layouts.app')
@section('title','Attendance')

@section('content')
<h2 class="mb-4">List Absensi</h2>

<form method="GET" class="row mb-3">
    <div class="col-md-3">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <select name="user_id" class="form-control">
            <option value="">-- Semua User --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id')==$user->id?'selected':'' }}>
                    {{ $user->username }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
    </div>
</form>

<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>Tanggal</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
            <th>Foto Bukti</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->tanggal->format('Y-m-d') }}</td>
            <td>{{ $attendance->user->username }}</td>
            <td>{{ $attendance->user->profile->nama_lengkap ?? '-' }}</td>
            <td>{{ optional($attendance->jam_masuk)->format('H:i:s') ?? '-' }}</td>
            <td>{{ optional($attendance->jam_pulang)->format('H:i:s') ?? '-' }}</td>
            <td>{{ ucfirst($attendance->status) }}</td>
            <td>
                @if($attendance->foto)
                    <a href="{{ asset('storage/'.$attendance->foto) }}" target="_blank">
                        <img src="{{ asset('storage/'.$attendance->foto) }}" width="60">
                    </a>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.attendance.show', $attendance->id) }}" class="btn btn-sm btn-info">
                    <i class="bi bi-eye"></i> Detail
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    <a href="{{ route('admin.attendance.export', [
        'user_id' => request('user_id'),
        'tanggal' => request('tanggal')
    ]) }}" class="btn btn-success">
        <i class="bi bi-file-earmark-arrow-down"></i> Export PDF/Excel
    </a>
</div>

@endsection
