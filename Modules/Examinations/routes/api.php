<?php

use Illuminate\Support\Facades\Route;
use Modules\Examinations\Http\Controllers\ExaminationsController;

Route::prefix('v1')->group(function () {
    Route::apiResource('examinations', ExaminationsController::class)->names('examinations');
});
