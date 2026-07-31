<?php

use Illuminate\Support\Facades\Route;
use Modules\Timetable\Http\Controllers\TimetableController;

Route::prefix('v1')->group(function () {
    Route::apiResource('timetables', TimetableController::class)->names('timetable');
});
