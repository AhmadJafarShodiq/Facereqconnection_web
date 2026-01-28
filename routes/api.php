<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\FaceController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| prefix otomatis: /api
|--------------------------------------------------------------------------
*/

/* =========================
| AUTH
| ========================= */
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post(
    '/logout',
    [AuthController::class, 'logout']
);

/* =========================
| USER BASIC
| ========================= */
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user()->load('profile');
});

/* =========================
| FACE (WAJIB SEBELUM PRESENSI)
| ========================= */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/face/register', [FaceController::class, 'register']);
    Route::post('/face/verify',   [FaceController::class, 'verify']);
    Route::get('/face/status',    [FaceController::class, 'status']);

});

/* =========================
| ROUTE TERKUNCI WAJAH
| ========================= */
Route::middleware(['auth:sanctum','face.verified'])->group(function () {

    /* ===== DASHBOARD ===== */
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /* ===== SUBJECT / MAPEL ===== */
    Route::get('/subjects', [SubjectController::class, 'index']);

    /* ===== JADWAL PELAJARAN ===== */
    Route::get('/schedules', [ScheduleController::class, 'studentSchedule']);
    Route::get('/schedules/teacher', [ScheduleController::class, 'teacherSchedule']);
    Route::get('/schedules/today', [ScheduleController::class, 'todaySchedule']);

    /* ===== SISWA PRESENSI ===== */
    Route::post(
        '/attendance/student',
        [AttendanceController::class,'studentCheckIn']
    );

    /* ===== GURU PRESENSI ===== */
    Route::post(
        '/attendance/teacher/check-in',
        [AttendanceController::class,'teacherCheckIn']
    );

    Route::post(
        '/attendance/teacher/check-out',
        [AttendanceController::class,'teacherCheckOut']
    );

    /* ===== GURU LIHAT SISWA BELUM ABSEN ===== */
    Route::get(
        '/attendance/subject/{subjectId}/missing',
        [AttendanceController::class,'missingStudents']
    );

    /* ===== GURU LIHAT ABSENSI SISWA PER MAPEL ===== */
    Route::get(
        '/attendance/subject/{subjectId}/today',
        [AttendanceController::class,'studentAttendanceBySubject']
    );

    /* ===== GURU LIHAT REPORT ABSENSI (RANGE TANGGAL) ===== */
    Route::get(
        '/attendance/subject/{subjectId}/report',
        [AttendanceController::class,'attendanceReport']
    );

    Route::get('attendance/today', [AttendanceController::class, 'today'])->middleware('auth:sanctum');

});

use App\Http\Controllers\Api\SchoolController;

Route::middleware(['auth:sanctum','face.verified'])->get('/school', [SchoolController::class, 'index']);
