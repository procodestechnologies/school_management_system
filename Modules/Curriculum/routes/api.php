<?php

use Illuminate\Support\Facades\Route;
use Modules\Curriculum\Http\Controllers\CurriculumController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('curricula', CurriculumController::class)->names('curriculum');
});
