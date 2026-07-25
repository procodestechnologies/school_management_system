<?php

use Illuminate\Support\Facades\Route;
use Modules\Student\Http\Controllers\StudentController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('students', StudentController::class)->names('student');
});
