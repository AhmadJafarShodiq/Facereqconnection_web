@extends('admin.layouts.app')
@section('title','Edit Mapel')

@section('content')
<div class="card">
    <div class="card-body">

        <form method="POST"
              action="{{ route('admin.subjects.update',$subject->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nama Mapel</label>
                <input type="text" name="nama_mapel"
                       value="{{ $subject->nama_mapel }}"
                       class="form-control" required>
            </div>

           <input type="hidden" name="kelas_id" value="{{ $subject->kelas_id }}">

            <button class="btn btn-warning">Update</button>
            <a href="{{ route('admin.subjects.index') }}"
               class="btn btn-secondary">Batal</a>
        </form>

    </div>
</div>
@endsection
