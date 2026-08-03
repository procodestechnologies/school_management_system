<?php

use Illuminate\Support\Facades\Route;
use Modules\Result\Http\Controllers\ResultController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('results', ResultController::class)->names('result');
});
