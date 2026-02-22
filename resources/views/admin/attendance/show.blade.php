@extends('admin.layouts.app')
@section('title','Detail Absensi')

@section('content')
<div class="card">
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item"><b>User:</b> {{ $attendance->user->username }}</li>
            <li class="list-group-item"><b>Nama:</b> {{ $attendance->user->profile->nama_lengkap ?? '-' }}</li>
            <li class="list-group-item"><b>Tanggal:</b> {{ $attendance->tanggal->format('Y-m-d') }}</li>
            <li class="list-group-item"><b>Mapel:</b> {{ $attendance->subject->nama_mapel ?? '-' }}</li>
            <li class="list-group-item"><b>Kelas:</b> {{ $attendance->kelas->nama_kelas ?? '-' }}</li>
            <li class="list-group-item"><b>Masuk:</b> {{ optional($attendance->jam_masuk)->format('H:i') }}</li>
            <li class="list-group-item"><b>Pulang:</b> {{ optional($attendance->jam_pulang)->format('H:i') }}</li>
            <li class="list-group-item"><b>Status:</b> {{ strtoupper($attendance->status) }}</li>
        </ul>

        @if($attendance->foto)
            <img src="{{ asset('storage/'.$attendance->foto) }}"
                 class="img-fluid mt-3">
        @endif

        <a href="{{ route('admin.attendance.index') }}"
           class="btn btn-secondary mt-3">
            Kembali
        </a>
    </div>
</div>
@endsection
