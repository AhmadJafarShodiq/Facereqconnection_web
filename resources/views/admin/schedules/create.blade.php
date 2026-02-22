@extends('admin.layouts.app')
@section('title','Tambah Jadwal')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST" action="{{ route('admin.schedules.store') }}">
            @csrf

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Guru</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">
                                {{ $g->profile->nama_lengkap ?? $g->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mapel</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan" class="form-control">
                </div>

            </div>

            <div class="text-end mt-4">
                <button class="btn btn-success">Simpan</button>
                <a href="{{ route('admin.schedules.index') }}"
                   class="btn btn-secondary">Batal</a>
            </div>

        </form>

    </div>
</div>
@endsection
