<?php

use Illuminate\Support\Facades\Route;
use Modules\Staff\Http\Controllers\StaffController;
use Modules\Staff\Http\Controllers\StaffPaymentController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('staff-payments', StaffPaymentController::class)
        ->names('staff.payments')
        ->parameters(['staff-payments' => 'payment']);
    Route::resource('staff', StaffController::class)->names('staff');
});
