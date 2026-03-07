<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\FaceDataController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SchoolController;


Route::get('/', fn() => redirect('/admin/login'));

// AUTH
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// ADMIN AREA
Route::middleware(['auth','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

     Route::resource('schedules', ScheduleController::class)
            ->only(['index','create','store','edit','update']);
            Route::post('schedules/import', [ScheduleController::class, 'import'])
    ->name('schedules.import');
  Route::delete('schedules/delete-all', 
    [ScheduleController::class, 'deleteAll']
)->name('schedules.deleteAll');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::resource('subjects', SubjectController::class)
            ->only(['index','create','store','edit','update']);
 
    // Master User
    Route::get('users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class)->except(['destroy']);
    Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
  
    // Profile
    Route::resource('profiles', ProfileController::class);

     Route::resource('classes', ClassController::class);
    // Face Data
    Route::get('face-data', [FaceDataController::class, 'index'])->name('face-data.index');
    Route::post('face-data/{user}/reset', [FaceDataController::class, 'reset'])->name('face-data.reset');

    Route::get('face-data', [FaceDataController::class,'index'])
            ->name('face-data.index');
    // Attendance (export harus di atas show supaya tidak bentrok)
    Route::get('attendance/pdf/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');

    // School Settings
    Route::get('school', [SchoolController::class, 'index'])->name('school.index');
    Route::post('school', [SchoolController::class, 'update'])->name('school.update');
});
