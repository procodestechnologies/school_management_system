<?php

use Illuminate\Support\Facades\Route;
use Modules\Curriculum\Http\Controllers\CurriculumController;

Route::prefix('v1')->group(function () {
    Route::apiResource('curricula', CurriculumController::class)->names('curriculum');
});
