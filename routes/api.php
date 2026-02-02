<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\FaceController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\GuruAttendanceSessionController;
use App\Http\Controllers\Api\SchoolController;


/* =================================================
| AUTH
================================================= */
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


/* =================================================
| USER BASIC
================================================= */
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user()->load('profile');
});


/* =================================================
| FACE (WAJIB SEBELUM MASUK DASHBOARD)
================================================= */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/face/register', [FaceController::class, 'register']);
    Route::post('/face/verify',   [FaceController::class, 'verify']);
    Route::get('/face/status',    [FaceController::class, 'status']);
});


/* =================================================
| ROUTE TERKUNCI WAJAH
================================================= */
Route::middleware(['auth:sanctum', 'face.verified'])->group(function () {

    /* ===== DASHBOARD ===== */
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/guru/dashboard/today', [DashboardController::class, 'guruToday']);

    /* ===== SEKOLAH ===== */
    Route::get('/school', [SchoolController::class, 'index']);

    /* ===== SUBJECT ===== */
    Route::get('/subjects', [SubjectController::class, 'index']);

    /* ===== JADWAL ===== */
    Route::get('/schedules', [ScheduleController::class, 'studentSchedule']);
    Route::get('/schedules/teacher', [ScheduleController::class, 'teacherSchedule']);
    Route::get('/schedules/today', [ScheduleController::class, 'todaySchedule']);

    /* ===== SESSION GURU ===== */
    Route::post('/guru/attendance/open', [GuruAttendanceSessionController::class, 'open']);
    Route::post('/guru/attendance/{id}/close', [GuruAttendanceSessionController::class, 'close']);

    /* =================================================
    | SISWA
    ================================================= */
    Route::post('/attendance/student', [AttendanceController::class, 'studentCheckIn']);
    Route::get('/attendance/history',  [AttendanceController::class, 'studentHistory']);
    Route::get('/attendance/today',    [AttendanceController::class, 'today']);

    /* =================================================
    | GURU
    ================================================= */
    Route::post('/attendance/teacher/check-in',  [AttendanceController::class, 'teacherCheckIn']);
    Route::post('/attendance/teacher/check-out', [AttendanceController::class, 'teacherCheckOut']);

    Route::get('/attendance/subject/{subjectId}/missing', [AttendanceController::class, 'missingStudents']);
    Route::get('/attendance/subject/{subjectId}/today',   [AttendanceController::class, 'studentAttendanceBySubject']);
    Route::get('/attendance/subject/{subjectId}/report',  [AttendanceController::class, 'attendanceReport']);
});
