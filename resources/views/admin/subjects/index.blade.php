@extends('admin.layouts.app')
@section('title','Mapel')

@section('content')
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-3">
            <form class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control form-control-sm"
                       placeholder="Cari mapel">
                <button class="btn btn-primary btn-sm">Cari</button>
            </form>

            <a href="{{ route('admin.subjects.create') }}"
               class="btn btn-success btn-sm">
                + Tambah Mapel
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
<tr>
    <th>Mapel</th>
    <th width="100">Aksi</th>
</tr>
</thead>
            <tbody>
@forelse($subjects as $s)
    <tr>
        <td>{{ $s->nama_mapel }}</td>
        <td>
            <a href="{{ route('admin.subjects.edit',$s->id) }}"
               class="btn btn-warning btn-sm">Edit</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="2" class="text-center">Kosong</td>
    </tr>
@endforelse
</tbody>
        </table>

        {{ $subjects->links() }}
    </div>
</div>
@endsection
