@extends('admin.layouts.app')
@section('title', 'Face Data')

@section('content')
    <h2 class="mb-4">Face Data Users</h2>
    @session('success')
        <div class="alert alert-success">
            {{session("success")}}
        </div>
    @endsession
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Username</th>
                <th>Nama</th>
                <th>Status Wajah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->profile->nama_lengkap ?? '-' }}</td>
                    <td>
                        @if($user->faceData)
                            <span class="badge bg-success">Terdaftar</span>
                        @else
                            <span class="badge bg-danger">Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($user->faceData)
                            <form action="{{ route('admin.face-data.reset', $user->id) }}" method="POST"
                                onsubmit="return confirm('Yakin reset wajah?');">
                                @csrf
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Reset</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
