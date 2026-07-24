<?php

use Illuminate\Support\Facades\Route;
use Modules\Timetable\Http\Controllers\TimetableController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('timetables', TimetableController::class)->names('timetable');
});
