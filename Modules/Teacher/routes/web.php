<?php

use Illuminate\Support\Facades\Route;
use Modules\Teacher\Http\Controllers\TeacherController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('teachers', TeacherController::class)->names('teacher');
});
