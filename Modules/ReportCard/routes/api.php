<?php

use Illuminate\Support\Facades\Route;
use Modules\ReportCard\Http\Controllers\ReportCardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('reportcards', [ReportCardController::class, 'index'])->name('reportcard.index');
    Route::get('reportcards/{reportcard}', [ReportCardController::class, 'show'])->name('reportcard.show');
});
