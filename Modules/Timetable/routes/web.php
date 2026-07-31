<?php

use Illuminate\Support\Facades\Route;
use Modules\Timetable\Http\Controllers\TimetableController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('timetables/import', [TimetableController::class, 'import'])->name('timetable.import');
    Route::post('timetables/import', [TimetableController::class, 'importStore'])->name('timetable.import.store');
    Route::get('timetables/import/template', [TimetableController::class, 'importTemplate'])->name('timetable.import.template');
    Route::resource('timetables', TimetableController::class)->names('timetable');
});
