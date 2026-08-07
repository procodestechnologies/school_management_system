<?php

use Illuminate\Support\Facades\Route;
use Modules\Institution\Http\Controllers\InstitutionController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('institutions', InstitutionController::class)->names('institution');
    Route::post('institutions/{institution}/approve', [InstitutionController::class, 'approve'])->name('institution.approve');
    Route::post('institutions/{institution}/choose', [InstitutionController::class, 'choose'])->name('institution.choose');
});
