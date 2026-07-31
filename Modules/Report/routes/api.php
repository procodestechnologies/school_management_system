<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::prefix('v1')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('report.index');
});
