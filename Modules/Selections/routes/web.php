<?php

use Illuminate\Support\Facades\Route;
use Modules\Selections\Http\Controllers\SelectionsController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('selections', [SelectionsController::class, 'index'])->name('selections.index');
    Route::post('selections', [SelectionsController::class, 'store'])->name('selections.store');
});
