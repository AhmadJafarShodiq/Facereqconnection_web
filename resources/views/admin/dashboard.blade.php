@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-0">Admin Dashboard</h4>
        <small class="text-muted">
            Selamat datang di Sistem Absensi Face Recognition
        </small>
    </div>

    <div class="row">

        {{-- MASTER USER --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Master User</h6>
                    <p class="text-muted mb-2">Manajemen akun login</p>
                    <span class="badge bg-primary">Admin Only</span>
                </div>
            </div>
        </div>

        {{-- PROFILE --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Profile</h6>
                    <p class="text-muted mb-2">Data identitas manusia</p>
                    <span class="badge bg-success">Tanpa Face</span>
                </div>
            </div>
        </div>

        {{-- FACE DATA --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Face Data</h6>
                    <p class="text-muted mb-2">Status & reset wajah</p>
                    <span class="badge bg-warning text-dark">Admin View</span>
                </div>
            </div>
        </div>

        {{-- ATTENDANCE --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold">Attendance</h6>
                    <p class="text-muted mb-2">Laporan kehadiran</p>
                    <span class="badge bg-info">Read Only</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
