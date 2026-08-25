<?php

use Illuminate\Support\Facades\Route;
use Modules\Subject\Http\Controllers\SubjectController;
use Modules\Subject\Http\Controllers\SubjectTeacherController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Declared before the resource so "subjects/teachers" isn't swallowed
    // by the {subject} show route.
    Route::get('subjects/teachers', [SubjectTeacherController::class, 'index'])->name('subject.teachers.index');
    Route::post('subjects/teachers', [SubjectTeacherController::class, 'store'])->name('subject.teachers.store');
    Route::delete('subjects/teachers/{assignment}', [SubjectTeacherController::class, 'destroy'])->name('subject.teachers.destroy');

    Route::resource('subjects', SubjectController::class)->names('subject');
});
