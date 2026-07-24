<?php

use Illuminate\Support\Facades\Route;
use Modules\Timetable\Http\Controllers\TimetableController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('timetables', TimetableController::class)->names('timetable');
});
