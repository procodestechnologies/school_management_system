<?php

use Illuminate\Support\Facades\Route;
use Modules\ReportCard\Http\Controllers\ReportCardController;
use Modules\ReportCard\Http\Controllers\ReportSettingsController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('report-cards/settings', [ReportSettingsController::class, 'edit'])->name('reportcard.settings');
    Route::put('report-cards/settings', [ReportSettingsController::class, 'updateTemplate'])->name('reportcard.settings.template');
    Route::post('report-cards/settings/grading-bands', [ReportSettingsController::class, 'storeGradingBand'])->name('reportcard.gradingbands.store');
    Route::delete('report-cards/settings/grading-bands/{gradingBand}', [ReportSettingsController::class, 'destroyGradingBand'])->name('reportcard.gradingbands.destroy');

    Route::get('report-cards', [ReportCardController::class, 'index'])->name('reportcard.index');
    Route::get('report-cards/{reportcard}', [ReportCardController::class, 'show'])->name('reportcard.show');
});
