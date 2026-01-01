<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route di sini otomatis pakai prefix /api
| Contoh: http://localhost:8000/api/login
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


use App\Http\Controllers\Api\AttendanceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);
});

use App\Http\Controllers\Api\SchoolController;

Route::get('/school', [SchoolController::class, 'index']);

use App\Http\Controllers\Api\FaceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/face/register', [FaceController::class, 'register']);
    Route::post('/face/verify', [FaceController::class, 'verify']);
    Route::get('/face/status', [FaceController::class, 'status']);
});
