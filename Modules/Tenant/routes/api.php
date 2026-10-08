<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\SchoolController;
use Modules\Tenant\Http\Controllers\SchoolSettingController;

Route::prefix('v1')->middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    // School Management Endpoints (Super Admin Only)
    Route::apiResource('schools', SchoolController::class);
    Route::patch('schools/{school}/status', [SchoolController::class, 'changeStatus'])->name('schools.status');

    // School Settings Endpoints (Super Admin Only)
    Route::get('schools/{school}/settings', [SchoolSettingController::class, 'show'])->name('schools.settings.show');
    Route::put('schools/{school}/settings', [SchoolSettingController::class, 'update'])->name('schools.settings.update');

    // Branding File Upload Endpoints (Super Admin Only)
    Route::post('schools/{school}/logo', [SchoolController::class, 'uploadLogo'])->name('schools.logo.upload');
    Route::post('schools/{school}/favicon', [SchoolSettingController::class, 'uploadFavicon'])->name('schools.favicon.upload');
});
