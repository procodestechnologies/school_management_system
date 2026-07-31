<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendance\Http\Controllers\AttendanceController;

Route::prefix('v1')->group(function () {
    Route::apiResource('attendances', AttendanceController::class)->names('attendance');

    // Fetched via same-origin browser JS from the Attendance dashboard page,
    // so it needs to participate in the session (the "api" middleware group
    // has no session handling) in order to see the logged-in user at all.
    Route::middleware('web')
        ->get('institutions/{institution}/attendance', [AttendanceController::class, 'institutionAttendance'])
        ->name('attendance.institution');
});
