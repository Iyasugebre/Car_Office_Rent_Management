<?php

use App\Models\Car;
use App\Models\Office;
use Illuminate\Support\Facades\Route;

// Auth Routes - Home is Login
Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'authenticate']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Admin Dashboard Routes - Unified ERP
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('offices', \App\Http\Controllers\Admin\OfficeManagerController::class);
    Route::resource('cars', \App\Http\Controllers\Admin\CarManagerController::class);
    Route::resource('bookings', \App\Http\Controllers\Admin\BookingManagerController::class);
    Route::resource('branch-requests', \App\Http\Controllers\Admin\BranchRequestController::class);
    Route::resource('agreements', \App\Http\Controllers\Admin\AgreementController::class);
    Route::post('agreements/{agreement}/authenticate', [\App\Http\Controllers\Admin\AgreementController::class, 'authenticate'])->name('agreements.authenticate');

    // Maintenance & Services
    Route::resource('services', \App\Http\Controllers\Admin\ServiceRequestController::class);
    Route::post('services/{service}/assign', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'assign'])->name('services.assign');
    Route::post('services/{service}/complete', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'complete'])->name('services.complete');

    // Renewal / Amendment / Termination
    Route::get('renewals', [\App\Http\Controllers\Admin\AgreementRenewalController::class, 'index'])->name('renewals.index');
    Route::get('agreements/{agreement}/renewals/create', [\App\Http\Controllers\Admin\AgreementRenewalController::class, 'create'])->name('renewals.create');
    Route::post('agreements/{agreement}/renewals', [\App\Http\Controllers\Admin\AgreementRenewalController::class, 'store'])->name('renewals.store');
    Route::post('renewals/{renewal}/approve', [\App\Http\Controllers\Admin\AgreementRenewalController::class, 'approve'])->name('renewals.approve');
    Route::post('renewals/{renewal}/reject', [\App\Http\Controllers\Admin\AgreementRenewalController::class, 'reject'])->name('renewals.reject');

    Route::post('notifications/mark-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markRead');
});
