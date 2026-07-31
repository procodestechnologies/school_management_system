<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('attendances', AttendanceController::class)->names('attendance');
    Route::get('institutions/{institution}/attendance', [AttendanceController::class, 'institutionAttendance'])
        ->name('attendance.institution');
});
