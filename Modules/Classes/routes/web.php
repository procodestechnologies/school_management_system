<?php

use Illuminate\Support\Facades\Route;
use Modules\Classes\Http\Controllers\ClassesController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('classes', ClassesController::class)->names('classes');
});
