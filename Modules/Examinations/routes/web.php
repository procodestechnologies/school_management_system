<?php

use Illuminate\Support\Facades\Route;
use Modules\Examinations\Http\Controllers\ExaminationsController;

/**
 * The screens are Livewire components; the controller keeps the write
 * endpoints for non-Livewire clients and the test suite.
 */
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::livewire('examinations', 'examinations::index')->name('examinations.index');
    Route::livewire('examinations/create', 'examinations::form')->name('examinations.create');
    // Parameter deliberately not named after the model - implicit binding
    // would hand mount() a model where it expects an id.
    Route::livewire('examinations/{examinationId}/edit', 'examinations::form')->name('examinations.edit');

    Route::resource('examinations', ExaminationsController::class)
        ->names('examinations')
        ->only(['store', 'show', 'update', 'destroy']);
});
