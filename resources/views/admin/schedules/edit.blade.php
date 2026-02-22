@extends('admin.layouts.app')
@section('title','Edit Jadwal')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        <form method="POST"
              action="{{ route('admin.schedules.update',$schedule->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Guru</label>
                    <select name="user_id" class="form-select" required>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}"
                                {{ $schedule->user_id == $g->id ? 'selected' : '' }}>
                                {{ $g->profile->nama_lengkap ?? $g->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mapel</label>
                    <select name="subject_id" class="form-select" required>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}"
                                {{ $schedule->subject_id == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}"
                                {{ $schedule->kelas_id == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hari</label>
                    <select name="hari" class="form-select" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                            <option value="{{ $h }}"
                                {{ $schedule->hari == $h ? 'selected' : '' }}>
                                {{ $h }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="jam_mulai"
                           value="{{ $schedule->jam_mulai }}"
                           class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="jam_selesai"
                           value="{{ $schedule->jam_selesai }}"
                           class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Ruangan</label>
                    <input type="text" name="ruangan"
                           value="{{ $schedule->ruangan }}"
                           class="form-control">
                </div>

            </div>

            <div class="text-end mt-4">
                <button class="btn btn-warning">Update</button>
                <a href="{{ route('admin.schedules.index') }}"
                   class="btn btn-secondary">Batal</a>
            </div>

        </form>

    </div>
</div>
@endsection
