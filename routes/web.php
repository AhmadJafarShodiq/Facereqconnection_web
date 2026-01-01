<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\FaceDataController;
use App\Http\Controllers\Admin\AttendanceController;

Route::get('/', fn () => redirect('/admin/login'));

// AUTH
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// ADMIN AREA
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Master User
    Route::resource('users', UserController::class)->except(['destroy']);

    // Profile
    Route::resource('profiles', ProfileController::class);

    // Face Data
    Route::get('face-data', [FaceDataController::class,'index'])->name('face-data.index');
    Route::post('face-data/{user}/reset', [FaceDataController::class,'reset'])->name('face-data.reset');

    // Attendance
    Route::get('attendance', [AttendanceController::class,'index'])->name('attendance.index');
    Route::get('attendance/{attendance}', [AttendanceController::class,'show'])->name('attendance.show');
    Route::get('attendance/export', [AttendanceController::class,'export'])->name('attendance.export');

});
