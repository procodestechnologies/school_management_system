<?php

use Illuminate\Support\Facades\Route;
use Modules\Parent\Http\Controllers\ParentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('parents', ParentController::class)->names('parent');
});
