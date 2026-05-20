<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
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
    ->middleware(['auth'/*, 'verified'*/]) // TODO: Re-enable 'verified' middleware for production
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Data Privacy Act agreement (must be accessible before DPA is agreed)
    Route::get('/dpa/accept', [\App\Http\Controllers\DpaController::class, 'show'])->name('dpa.accept');
    Route::post('/dpa/accept', [\App\Http\Controllers\DpaController::class, 'accept'])->name('dpa.accept.store');

    // Profile completion (required for new users before accessing the system)
    Route::get('/profile/complete', [\App\Http\Controllers\ProfileCompletionController::class, 'show'])->name('profile.complete');
    Route::post('/profile/complete', [\App\Http\Controllers\ProfileCompletionController::class, 'store'])->middleware('throttle:10,1')->name('profile.complete.store');

    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile');
    Route::get('/account/profile/photo', [ProfileController::class, 'photo'])->name('account.profile.photo');
    Route::get('/users/{user}/photo', [ProfileController::class, 'photoForUser'])->name('profile.photo');
    Route::get('/account/certificates', [ProfileController::class, 'certificates'])->name('account.certificates');
    Route::get('/account/certificates/{certificate}', [CertificateController::class, 'show'])->name('account.certificates.show');
    Route::get('/account/documents', [ProfileController::class, 'documents'])->name('account.documents');
    Route::get('/account/notifications', [ProfileController::class, 'notifications'])->name('account.notifications');
    Route::patch('/account/notifications/read-all', [ProfileController::class, 'notificationsMarkAllRead'])->name('account.notifications.mark-all-read');
    Route::patch('/account/notifications/{id}/read', [ProfileController::class, 'notificationMarkRead'])->name('account.notifications.mark-read');
    Route::delete('/account/notifications/{id}', [ProfileController::class, 'notificationDelete'])->name('account.notifications.delete');
    Route::get('/account/settings', [ProfileController::class, 'settings'])->name('account.settings');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->middleware('throttle:10,1')->name('account.profile.update');
    Route::patch('/account/profile/photo', [ProfileController::class, 'removePhoto'])->middleware('throttle:profile-mutations')->name('account.profile.photo.remove');
    Route::patch('/account/profile/details', [ProfileDetailsController::class, 'update'])->middleware('throttle:profile-mutations')->name('account.profile.details');
    Route::delete('/account/profile', [ProfileController::class, 'destroy'])->middleware('throttle:profile-mutations')->name('account.profile.destroy');

    Route::post('/certificates', [CertificateController::class, 'store'])->middleware('throttle:10,1')->name('certificates.store');
    Route::patch('/certificates/{certificate}', [CertificateController::class, 'update'])->name('certificates.update');
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

    // Notification polling (used by the nav panel to detect new notifications without a page reload)
    Route::get('/notifications/poll',  [\App\Http\Controllers\NotificationPollController::class, 'poll'])->name('notifications.poll')->middleware('throttle:30,1');
    Route::get('/notifications/panel', [\App\Http\Controllers\NotificationPollController::class, 'panel'])->name('notifications.panel')->middleware('throttle:30,1');

    // Dashboard live-data polling (60-second interval refresh of stat cards and activity)
    Route::get('/dashboard/live', [ProfileController::class, 'dashboardLive'])->name('dashboard.live')->middleware('throttle:60,1');
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/live', [AdminDashboardController::class, 'live'])->name('dashboard.live')->middleware('throttle:60,1');

        // Toast notification test page
        Route::view('/toast-test', 'admin.toast-test')->name('toast-test');
        
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->middleware('throttle:admin-password-reset')->name('users.reset-password');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/certificates', [AdminCertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [AdminCertificateController::class, 'show'])->name('certificates.show');
        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}', [AdminDocumentController::class, 'show'])->name('documents.show');
        // Announcements
        Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->middleware('throttle:10,1')->name('announcements.store');
        // Notifications
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.mark-read');
        Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [AdminNotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
        // Activity log
        Route::get('/activity', [AdminActivityController::class, 'index'])->name('activity.index');
        // Backups
        Route::get('/backups', [AdminBackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/run', [AdminBackupController::class, 'run'])->middleware('throttle:admin-backups')->name('backups.run');
        Route::get('/backups/download', [AdminBackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups', [AdminBackupController::class, 'destroy'])->middleware('throttle:admin-backups')->name('backups.destroy');
        Route::post('/backups/restore', [AdminBackupController::class, 'restore'])->middleware('throttle:admin-backups')->name('backups.restore');
        Route::post('/backups/restore-upload', [AdminBackupController::class, 'restoreFromUpload'])->middleware('throttle:admin-backups')->name('backups.restore-upload');
        Route::post('/backups/schedule', [AdminBackupController::class, 'saveSchedule'])->name('backups.schedule');
        // Settings
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        // Exports
        Route::get('/export/certificates', [AdminExportController::class, 'certificates'])->middleware('throttle:admin-exports')->name('export.certificates');
        Route::get('/export/users', [AdminExportController::class, 'users'])->middleware('throttle:admin-exports')->name('export.users');
    });

require __DIR__.'/auth.php';
