<?php

use Illuminate\Support\Facades\Route;
use Modules\Result\Http\Controllers\ResultController;
use Modules\Result\Http\Controllers\ResultEntryController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Declared before the resource so "results/enter" isn't swallowed by
    // the {result} show route.
    Route::get('results/enter', [ResultEntryController::class, 'create'])->name('result.entry.create');
    Route::post('results/enter', [ResultEntryController::class, 'store'])->name('result.entry.store');

    Route::resource('results', ResultController::class)->names('result');
});
