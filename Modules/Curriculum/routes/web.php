<?php

use Illuminate\Support\Facades\Route;
use Modules\Curriculum\Http\Controllers\CurriculumController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('curricula', CurriculumController::class)->names('curriculum');
});
