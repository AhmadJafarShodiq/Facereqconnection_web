@extends('admin.layouts.app')
@section('title', 'Face Data')

@section('content')
<div class="card">
    <div class="card-body">

        <h5 class="mb-3">Face Data Users</h5>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Status Wajah</th>
                    <th width="120">Aksi</th>
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
                                <form method="POST"
                                      action="{{ route('admin.face-data.reset',$user->id) }}"
                                      onsubmit="return confirm('Yakin reset wajah?')">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Reset
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
