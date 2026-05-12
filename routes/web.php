<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
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
    Route::get('/account/certificates', [ProfileController::class, 'certificates'])->name('account.certificates');
    Route::get('/account/documents', [ProfileController::class, 'documents'])->name('account.documents');
    Route::get('/account/notifications', [ProfileController::class, 'notifications'])->name('account.notifications');
    Route::get('/account/settings', [ProfileController::class, 'settings'])->name('account.settings');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::patch('/account/profile/details', [ProfileDetailsController::class, 'update'])->name('account.profile.details');
    Route::delete('/account/profile', [ProfileController::class, 'destroy'])->name('account.profile.destroy');

    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');

    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
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
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::view('/certificates', 'admin.certificates.index')->name('certificates.index');
        Route::view('/documents', 'admin.documents.index')->name('documents.index');
        Route::view('/notifications', 'admin.notifications.index')->name('notifications.index');
        Route::view('/activity', 'admin.activity.index')->name('activity.index');
        Route::view('/backups', 'admin.backups.index')->name('backups.index');
        Route::view('/settings', 'admin.settings.index')->name('settings.index');
    });

require __DIR__.'/auth.php';
