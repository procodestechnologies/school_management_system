<?php

use Illuminate\Support\Facades\Route;
use Modules\FeeManagement\Http\Controllers\FeeManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('feemanagements', FeeManagementController::class)->names('feemanagement');
});
