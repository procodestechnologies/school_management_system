<?php

use Illuminate\Support\Facades\Route;
use Modules\FeeManagement\Http\Controllers\FeeManagementController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('feemanagements', FeeManagementController::class)->names('feemanagement');
});
