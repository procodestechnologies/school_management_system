<?php

use Illuminate\Support\Facades\Route;
use Modules\Result\Http\Controllers\ResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('results', ResultController::class)->names('result');
});
