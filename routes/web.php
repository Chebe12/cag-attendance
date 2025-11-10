<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\AttendanceController;

// ============================================================================
// PUBLIC ROUTES - Accessible to everyone
// ============================================================================

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

// Welcome page - Redirect to appropriate location
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// ============================================================================
// AUTHENTICATED ROUTES - Requires login
// ============================================================================

Route::middleware('auth')->group(function () {

    // Main dashboard - Redirects based on user type
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isInstructor() || $user->isOfficeStaff()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('login');
    })->name('dashboard');

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

// ============================================================================
// ADMIN ROUTES - Requires admin user_type
// ============================================================================

Route::middleware(['auth', 'check.user.type:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management (CRUD)
    Route::resource('users', UserController::class);

    // Clients Management (CRUD)
    Route::resource('clients', ClientController::class);

    // Shifts Management (CRUD)
    Route::resource('shifts', ShiftController::class);

    // Schedules Management (CRUD)
    Route::resource('schedules', ScheduleController::class);

    // QR Codes Management
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::get('/', [QrCodeController::class, 'index'])->name('index');
        Route::get('/create', [QrCodeController::class, 'create'])->name('create');
        Route::post('/', [QrCodeController::class, 'store'])->name('store');
        Route::get('/{qrCode}/download', [QrCodeController::class, 'download'])->name('download');
        Route::post('/{qrCode}/generate', [QrCodeController::class, 'generate'])->name('generate');
        Route::get('/{qrCode}', [QrCodeController::class, 'show'])->name('show');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/schedules', [ReportController::class, 'schedules'])->name('schedules');
        Route::post('/export', [ReportController::class, 'export'])->name('export');
    });

});

// ============================================================================
// STAFF ROUTES - Requires instructor or office_staff user_type
// ============================================================================

Route::middleware(['auth', 'check.user.type:instructor,office_staff'])->prefix('staff')->name('staff.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Attendance Management
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/mark', [AttendanceController::class, 'mark'])->name('mark');
        Route::post('/scan', [AttendanceController::class, 'scan'])->name('scan');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
    });

    // My Schedule
    Route::get('/schedule', [AttendanceController::class, 'mySchedule'])->name('my-schedule');

    // My Clients (Instructors only)
    Route::get('/clients', [AttendanceController::class, 'myClients'])
        ->middleware('check.user.type:instructor')
        ->name('my-clients');

});

// ============================================================================
// FALLBACK - Undefined routes
// ============================================================================

// This will catch any undefined routes and return a 404
Route::fallback(function () {
    abort(404, 'Page not found.');
});
