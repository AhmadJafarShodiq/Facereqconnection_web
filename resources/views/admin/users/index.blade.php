@extends('admin.layouts.app')

@section('title', 'Master User')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

     <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
  <div class="d-flex justify-content-between align-items-center mb-3 gap-2">

    <form method="GET" id="filterForm" class="d-flex gap-2 mb-0">

        {{-- Search Username --}}
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               class="form-control form-control-sm" 
               placeholder="Cari username..."
               onkeyup="delaySubmit()">

        {{-- Filter Role --}}
        <select name="role"
                class="form-select form-select-sm"
                onchange="document.getElementById('filterForm').submit()">

            <option value="">Semua Role</option>
            <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
            <option value="guru" {{ request('role')=='guru'?'selected':'' }}>Guru</option>
            <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>Siswa</option>
        </select>

    </form>

    <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus"></i> Tambah
    </a>
</div>

    
</div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                       <td>
    @if($user->role === 'siswa' && $user->profile && $user->profile->kelas)
        {{ $user->profile->kelas->nama_kelas }}
    @else
        -
    @endif
</td>

                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success':'bg-danger' }}">
                                {{ $user->is_active ? 'Aktif':'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show',$user->id) }}"
                               class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>

                            <a href="{{ route('admin.users.edit',$user->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('admin.users.toggle',$user->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                <button class="btn btn-sm {{ $user->is_active?'btn-danger':'btn-success' }}">
                                    {{ $user->is_active?'Nonaktifkan':'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data kosong</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>

    </div>
</div>

@endsection
