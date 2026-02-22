@extends('admin.layouts.app')

@section('title','Detail User')

@section('content')

<div class="card">
    <div class="card-body">

        <h4 class="mb-3">Informasi Akun</h4>

        <p><strong>Username:</strong> {{ $user->username }}</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        <p><strong>Status:</strong> 
            <span class="badge {{ $user->is_active ? 'bg-success':'bg-danger' }}">
                {{ $user->is_active ? 'Aktif':'Nonaktif' }}
            </span>
        </p>

        <hr>

        <h4 class="mb-3">Informasi Profile</h4>

        @if($user->profile)

            <p><strong>Nama Lengkap:</strong> {{ $user->profile->nama_lengkap }}</p>
            <p><strong>NIP / NIS:</strong> {{ $user->profile->nip_nis }}</p>
            <p><strong>Instansi:</strong> {{ $user->profile->instansi }}</p>

            {{-- Jika SISWA --}}
            @if($user->role === 'siswa')
                <p><strong>Kelas:</strong> 
                    {{ $user->profile->kelas->nama_kelas ?? '-' }}
                </p>
            @endif

            {{-- Jika GURU --}}
            @if($user->role === 'guru')

                <hr>
                <h5>Mapel yang Diajar</h5>

                @forelse($user->subjects as $subject)
                    <span class="badge bg-primary">
                        {{ $subject->nama_mapel }}
                    </span>
                @empty
                    <p>- Belum ada mapel</p>
                @endforelse

            @endif

        @else
            <p class="text-muted">Profile belum dibuat.</p>
        @endif

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

    </div>
</div>

@endsection
