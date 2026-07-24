<?php

use Illuminate\Support\Facades\Route;
use Modules\Parent\Http\Controllers\ParentController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('parents', ParentController::class)->names('parent');
});
