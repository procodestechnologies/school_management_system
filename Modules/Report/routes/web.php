<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('report.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('report.export');
});
