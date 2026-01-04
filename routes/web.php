<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ScheduleCategoryController;
use App\Http\Controllers\Admin\BulkScheduleController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\AttendanceController;

// ============================================================================
// PUBLIC ROUTES - Accessible to everyone
// ============================================================================

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

// Register route (stub - redirect to login for now)
Route::get('/register', function () {
    return redirect()->route('login')->with('info', 'Registration is currently managed by administrators.');
})->name('register');

// Password reset route (stub - redirect to login for now)
Route::get('/password/reset', function () {
    return redirect()->route('login')->with('info', 'Please contact your administrator for password reset assistance.');
})->name('password.request');

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

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// ============================================================================
// ADMIN ROUTES - Requires admin user_type
// ============================================================================

Route::middleware(['auth', 'check.user.type:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management (CRUD)
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);

    // Clients Management (CRUD)
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::resource('clients', ClientController::class);

    // Shifts Management (CRUD)
    Route::resource('shifts', ShiftController::class);

    // Schedules Management (CRUD)
    Route::get('/schedules/export', [ScheduleController::class, 'export'])->name('schedules.export');
    Route::get('/schedules/availability', [ScheduleController::class, 'availability'])->name('schedules.availability');
    Route::get('/schedules/print', [ScheduleController::class, 'printView'])->name('schedules.print');
    Route::get('/schedules/weekly', [ScheduleController::class, 'weeklyOverview'])->name('schedules.weekly');
    Route::resource('schedules', ScheduleController::class);

    // Schedule Categories/Terms Management
    Route::resource('schedule-categories', ScheduleCategoryController::class);
    Route::patch('/schedule-categories/{scheduleCategory}/activate', [ScheduleCategoryController::class, 'activate'])->name('schedule-categories.activate');
    Route::patch('/schedule-categories/{scheduleCategory}/archive', [ScheduleCategoryController::class, 'archive'])->name('schedule-categories.archive');

    // Bulk Schedule Creation
    Route::prefix('schedules/bulk')->name('schedules.bulk.')->group(function () {
        Route::get('/create', [BulkScheduleController::class, 'create'])->name('create');
        Route::post('/store', [BulkScheduleController::class, 'store'])->name('store');
        Route::post('/validate', [BulkScheduleController::class, 'validate'])->name('validate');
        Route::post('/{category}/publish', [BulkScheduleController::class, 'publish'])->name('publish');
        Route::delete('/{category}/drafts', [BulkScheduleController::class, 'deleteDrafts'])->name('delete-drafts');
    });

    // Departments Management (CRUD)
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);

    // Attendance Management (View only for admin)
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AdminAttendanceController::class, 'index'])->name('index');
        Route::get('/{attendance}', [AdminAttendanceController::class, 'show'])->name('show');
        Route::delete('/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('destroy');
    });

    // QR Codes Management
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::get('/', [QrCodeController::class, 'index'])->name('index');
        Route::get('/create', [QrCodeController::class, 'create'])->name('create');
        Route::post('/', [QrCodeController::class, 'store'])->name('store');
        Route::get('/{qrCode}/download', [QrCodeController::class, 'download'])->name('download');
        Route::get('/{qrCode}/print', [QrCodeController::class, 'print'])->name('print');
        Route::post('/{qrCode}/generate', [QrCodeController::class, 'generate'])->name('generate');
        Route::patch('/{qrCode}/activate', [QrCodeController::class, 'activate'])->name('activate');
        Route::patch('/{qrCode}/deactivate', [QrCodeController::class, 'deactivate'])->name('deactivate');
        Route::get('/{qrCode}/edit', [QrCodeController::class, 'edit'])->name('edit');
        Route::put('/{qrCode}', [QrCodeController::class, 'update'])->name('update');
        Route::delete('/{qrCode}', [QrCodeController::class, 'destroy'])->name('destroy');
        Route::get('/{qrCode}', [QrCodeController::class, 'show'])->name('show');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/schedules', [ReportController::class, 'schedules'])->name('schedules');
        Route::get('/statistics', [ReportController::class, 'statistics'])->name('statistics');
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
        Route::post('/manual', [AttendanceController::class, 'checkIn'])->name('manual');
        Route::post('/process', [AttendanceController::class, 'scan'])->name('process');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });

    // Schedules (My Schedule)
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [AttendanceController::class, 'mySchedule'])->name('index');
    });

    // Legacy route - keep for backward compatibility
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
