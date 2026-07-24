<?php

use Illuminate\Support\Facades\Route;
use Modules\Examinations\Http\Controllers\ExaminationsController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('examinations', ExaminationsController::class)->names('examinations');
});
