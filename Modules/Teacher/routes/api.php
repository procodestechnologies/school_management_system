<?php

use Illuminate\Support\Facades\Route;
use Modules\Teacher\Http\Controllers\TeacherController;

Route::prefix('v1')->group(function () {
    Route::apiResource('teachers', TeacherController::class)->names('teacher');
});
