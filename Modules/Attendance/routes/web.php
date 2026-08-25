<?php

use Illuminate\Support\Facades\Route;

/**
 * The log viewer is a Livewire component - filtering and paging never
 * reload the page. There is no CRUD here: attendance scans are written by
 * the biometric devices, and the JSON endpoint lives in api.php.
 */
Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::livewire('attendances', 'attendance::index')->name('attendance.index');
});
