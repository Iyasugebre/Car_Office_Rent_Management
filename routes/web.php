<?php

use Illuminate\Support\Facades\Route;

// Auth Routes - Home is Login
Route::get('/', [\App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'authenticate']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Custom Blade ERP Routes - moved to /erp prefix to avoid conflict with Filament's /admin panel
Route::middleware(['auth'])->prefix('erp')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('offices', \App\Http\Controllers\Admin\OfficeManagerController::class);
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

    // Vehicle Service Tracker
    Route::get('service-tracker', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'index'])->name('service-tracker.index');
    Route::get('service-tracker/schedules', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'schedules'])->name('service-tracker.schedules');
    Route::get('service-tracker/schedules/create', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'createSchedule'])->name('service-tracker.schedules.create');
    Route::post('service-tracker/schedules', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'storeSchedule'])->name('service-tracker.schedules.store');
    Route::post('service-tracker/schedules/{schedule}/toggle', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'toggleSchedule'])->name('service-tracker.schedules.toggle');
    Route::delete('service-tracker/schedules/{schedule}', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'destroySchedule'])->name('service-tracker.schedules.destroy');
    Route::get('service-tracker/{car}', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'show'])->name('service-tracker.show');
    Route::post('service-tracker/{car}/log-service', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'logService'])->name('service-tracker.log-service');
    Route::post('service-tracker/{car}/update-mileage', [\App\Http\Controllers\Admin\VehicleServiceTrackerController::class, 'updateMileage'])->name('service-tracker.update-mileage');

    // Vehicle Legal Tracker (Bolo & Inspection)
    Route::prefix('legal-tracker')->name('legal-tracker.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VehicleLegalTrackerController::class, 'index'])->name('index');
        Route::get('/{car}/edit', [\App\Http\Controllers\Admin\VehicleLegalTrackerController::class, 'edit'])->name('edit');
        Route::put('/{car}', [\App\Http\Controllers\Admin\VehicleLegalTrackerController::class, 'update'])->name('update');
    });

    // Alert Center (ERP Notification Engine)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NotificationCenterController::class, 'index'])->name('index');
        Route::post('/{id}/read', [\App\Http\Controllers\Admin\NotificationCenterController::class, 'markAsRead'])->name('markRead');
        Route::post('/mark-all-read', [\App\Http\Controllers\Admin\NotificationCenterController::class, 'markAllRead'])->name('markAllRead');
    });

    // Branch Utilities
    Route::prefix('branch-utilities')->name('branch-utilities.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BranchUtilityController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\BranchUtilityController::class, 'store'])->name('store');
        Route::put('/{utility}', [\App\Http\Controllers\Admin\BranchUtilityController::class, 'update'])->name('update');
        Route::post('/{utility}/pay', [\App\Http\Controllers\Admin\BranchUtilityController::class, 'recordPayment'])->name('recordPayment');
        Route::delete('/{utility}', [\App\Http\Controllers\Admin\BranchUtilityController::class, 'destroy'])->name('destroy');
    });
});
