@extends('admin.layouts.app')
@section('title', 'Pengaturan Sekolah')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-modern rounded-modern">
            <div class="card-header bg-transparent border-0 py-4">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-gear-fill text-primary me-2"></i>Pengaturan Sekolah & Lokasi
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.school.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">NAMA SEKOLAH</label>
                        <input type="text" name="nama_sekolah" class="form-control" value="{{ $school->nama_sekolah ?? '' }}" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LATITUDE</label>
                            <input type="text" name="latitude" class="form-control" value="{{ $school->latitude ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LONGITUDE</label>
                            <input type="text" name="longitude" class="form-control" value="{{ $school->longitude ?? '' }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">RADIUS PRESENSI (METER)</label>
                        <div class="input-group">
                            <input type="number" name="radius" class="form-control" value="{{ $school->radius ?? '' }}" required>
                            <span class="input-group-text bg-light border-start-0 text-muted">Meter</span>
                        </div>
                        <small class="text-muted mt-2 d-block">Radius maksimal siswa dapat melakukan presensi dari titik koordinat sekolah.</small>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">LOGO SEKOLAH</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            @if(isset($school->logo))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="img-thumbnail" style="height: 50px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">WARNA TEMA APLIKASI</label>
                            <div class="input-group">
                                <input type="color" name="primary_color" class="form-control form-control-color" value="{{ $school->primary_color ?? '#563d7c' }}" title="Pilih warna tema">
                                <input type="text" class="form-control" value="{{ $school->primary_color ?? '#563d7c' }}" readonly>
                            </div>
                            <small class="text-muted mt-1 d-block">Warna ini akan menjadi warna utama di aplikasi mobile.</small>
                        </div>
                    </div>

                    <hr class="my-4 opacity-50">

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-modern rounded-modern mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-info me-2"></i>Cara Mendapatkan Koordinat</h6>
                <p class="small text-muted mb-0">
                    1. Buka Google Maps di browser.<br>
                    2. Klik kanan pada lokasi gedung sekolah Anda.<br>
                    3. Pilih angka koordinat yang muncul (Latitude, Longitude).<br>
                    4. Masukkan ke form di atas secara terpisah.
                </p>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    document.querySelector('input[name="primary_color"]').addEventListener('input', function(e) {
        this.nextElementSibling.value = e.target.value;
    });
</script>
@endpush
@endsection
