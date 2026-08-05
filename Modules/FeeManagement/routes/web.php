<?php

use Illuminate\Support\Facades\Route;
use Modules\FeeManagement\Http\Controllers\FeeManagementController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::post('feemanagements/send-reminders', [FeeManagementController::class, 'sendReminders'])->name('feemanagement.send-reminders');
    Route::resource('feemanagements', FeeManagementController::class)->names('feemanagement');
});
