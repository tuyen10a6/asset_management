<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;

// Authentication routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// QR Code public access (for scanning)
Route::get('/{asset_code}', [AssetController::class, 'showAssetByQR'])->name('qr.asset');
Route::get('/scan', function() {
    return view('qr.scan');
})->name('qr.scan.public');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Asset management
    Route::resource('assets', AssetController::class);

    // Employee management
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees/{employee}/assignments', [EmployeeController::class, 'getAssignments'])->name('employees.assignments');

    // Department management
    Route::resource('departments', DepartmentController::class);
    Route::get('/departments/{department}/employees', [DepartmentController::class, 'getEmployees'])->name('departments.employees');

    // Asset assignments
    Route::get('/assignments/expiring', [AssignmentController::class, 'getExpiringAssignments'])->name('assignments.expiring');
    Route::resource('assignments', AssignmentController::class);
    Route::get('/assignments/{assignment}/return', [AssignmentController::class, 'showReturnForm'])->name('assignments.return.form');
    Route::post('/assignments/{assignment}/return', [AssignmentController::class, 'return'])->name('assignments.return');

    // Incident management
    Route::resource('incidents', IncidentController::class);
    Route::post('/incidents/{incident}/resolve', [IncidentController::class, 'resolve'])->name('incidents.resolve');
    Route::post('/incidents/{incident}/close', [IncidentController::class, 'close'])->name('incidents.close');
    Route::get('/assets/{asset}/incidents', [IncidentController::class, 'getAssetIncidents'])->name('assets.incidents');

    // Notifications
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'destroy']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

    // User management (Admin only)
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Settings management (Admin only)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::post('/profile/notifications/{id}/read', [ProfileController::class, 'markNotificationAsRead'])->name('profile.notifications.read');
    Route::post('/profile/notifications/read-all', [ProfileController::class, 'markAllNotificationsAsRead'])->name('profile.notifications.read-all');
    Route::delete('/profile/notifications/{id}', [ProfileController::class, 'deleteNotification'])->name('profile.notifications.delete');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');

    // QR Code routes
    Route::get('/assets/{asset}/qr', [AssetController::class, 'showQR'])->name('assets.qr');
    Route::get('/qr-scan', function() {
        return view('qr.scan');
    })->name('qr.scan');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/assets', [ReportController::class, 'assetReport'])->name('reports.assets');
    Route::get('/reports/activities', [ReportController::class, 'activityReport'])->name('reports.activities');
    Route::get('/reports/export', [ReportController::class, 'exportAssets'])->name('reports.export');
});
