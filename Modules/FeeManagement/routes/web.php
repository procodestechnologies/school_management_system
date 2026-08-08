<?php

use Illuminate\Support\Facades\Route;
use Modules\FeeManagement\Http\Controllers\FeeManagementController;
use Modules\FeeManagement\Http\Controllers\FeeReceiptController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::post('feemanagements/send-reminders', [FeeManagementController::class, 'sendReminders'])->name('feemanagement.send-reminders');
    Route::get('feemanagements/receipts/create', [FeeReceiptController::class, 'create'])->name('feemanagement.receipts.create');
    Route::post('feemanagements/receipts/extract', [FeeReceiptController::class, 'extract'])->name('feemanagement.receipts.extract');
    Route::get('feemanagements/receipts/student-fees', [FeeReceiptController::class, 'studentFees'])->name('feemanagement.receipts.student-fees');
    Route::post('feemanagements/receipts', [FeeReceiptController::class, 'store'])->name('feemanagement.receipts.store');
    Route::resource('feemanagements', FeeManagementController::class)->names('feemanagement');
});
