<?php

use Illuminate\Support\Facades\Route;
use Modules\Lesson\Http\Controllers\LessonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lessons', LessonController::class)->names('lesson');
});
