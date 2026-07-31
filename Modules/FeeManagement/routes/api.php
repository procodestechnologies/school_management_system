<?php

use Illuminate\Support\Facades\Route;
use Modules\FeeManagement\Http\Controllers\FeeManagementController;

Route::prefix('v1')->group(function () {
    Route::apiResource('feemanagements', FeeManagementController::class)->names('feemanagement');
});
