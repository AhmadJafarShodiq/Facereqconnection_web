@extends('admin.layouts.app')
@section('title','Detail Attendance')

@section('content')
<h2>Detail Absensi</h2>
<ul class="list-group mb-3">
    <li class="list-group-item"><strong>Username:</strong> {{ $attendance->user->username }}</li>
    <li class="list-group-item"><strong>Nama:</strong> {{ $attendance->user->profile->nama_lengkap ?? '-' }}</li>
    <li class="list-group-item"><strong>Tanggal:</strong> {{ $attendance->tanggal->format('Y-m-d') }}</li>
    <li class="list-group-item"><strong>Jam Masuk:</strong> {{ optional($attendance->jam_masuk)->format('H:i:s') ?? '-' }}</li>
    <li class="list-group-item"><strong>Jam Pulang:</strong> {{ optional($attendance->jam_pulang)->format('H:i:s') ?? '-' }}</li>
    <li class="list-group-item"><strong>Status:</strong> {{ ucfirst($attendance->status) }}</li>
    <li class="list-group-item"><strong>Foto Bukti:</strong>
        @if($attendance->foto)
            <img src="{{ asset('storage/'.$attendance->foto) }}" width="150">
        @endif
    </li>
</ul>
<a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
    <i class="bi bi-arrow-left"></i> Kembali
</a>
@endsection
