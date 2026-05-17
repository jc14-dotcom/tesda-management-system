<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\BackupController as AdminBackupController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDetailsController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/dashboard', [ProfileController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile');
    Route::get('/account/profile/photo', [ProfileController::class, 'photo'])->name('account.profile.photo');
    Route::get('/users/{user}/photo', [ProfileController::class, 'photoForUser'])->name('profile.photo');
    Route::get('/account/certificates', [ProfileController::class, 'certificates'])->name('account.certificates');
    Route::get('/account/documents', [ProfileController::class, 'documents'])->name('account.documents');
    Route::get('/account/notifications', [ProfileController::class, 'notifications'])->name('account.notifications');
    Route::patch('/account/notifications/read-all', [ProfileController::class, 'notificationsMarkAllRead'])->name('account.notifications.mark-all-read');
    Route::patch('/account/notifications/{id}/read', [ProfileController::class, 'notificationMarkRead'])->name('account.notifications.mark-read');
    Route::delete('/account/notifications/{id}', [ProfileController::class, 'notificationDelete'])->name('account.notifications.delete');
    Route::get('/account/settings', [ProfileController::class, 'settings'])->name('account.settings');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->middleware('throttle:10,1')->name('account.profile.update');
    Route::patch('/account/profile/photo', [ProfileController::class, 'removePhoto'])->name('account.profile.photo.remove');
    Route::patch('/account/profile/details', [ProfileDetailsController::class, 'update'])->name('account.profile.details');
    Route::delete('/account/profile', [ProfileController::class, 'destroy'])->name('account.profile.destroy');

    Route::post('/certificates', [CertificateController::class, 'store'])->middleware('throttle:10,1')->name('certificates.store');
    Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');

    Route::post('/documents', [DocumentController::class, 'store'])->middleware('throttle:10,1')->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'view'])
        ->name('documents.view');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
        ->name('documents.preview');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])
        ->name('documents.destroy');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
        Route::patch('/certificates/{certificate}/verify', [AdminCertificateController::class, 'verify'])->name('certificates.verify');
        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [AdminNotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
        // Activity log
        Route::get('/activity', [AdminActivityController::class, 'index'])->name('activity.index');
        // Backups
        Route::get('/backups', [AdminBackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/run', [AdminBackupController::class, 'run'])->name('backups.run');
        Route::get('/backups/download', [AdminBackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups', [AdminBackupController::class, 'destroy'])->name('backups.destroy');
        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        // Exports
        Route::get('/export/certificates', [AdminExportController::class, 'certificates'])->name('export.certificates');
        Route::get('/export/users', [AdminExportController::class, 'users'])->name('export.users');
    });

require __DIR__.'/auth.php';
