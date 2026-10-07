<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\SchoolController;
use Modules\Tenant\Http\Controllers\SchoolSettingController;

Route::prefix('v1')->group(function () {
    // School Management Endpoints
    Route::apiResource('schools', SchoolController::class);
    Route::patch('schools/{school}/status', [SchoolController::class, 'changeStatus'])->name('schools.status');

    // School Settings Endpoints
    Route::get('schools/{school}/settings', [SchoolSettingController::class, 'show'])->name('schools.settings.show');
    Route::put('schools/{school}/settings', [SchoolSettingController::class, 'update'])->name('schools.settings.update');
});
