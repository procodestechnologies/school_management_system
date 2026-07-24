<?php

use Illuminate\Support\Facades\Route;
use Modules\Parent\Http\Controllers\ParentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parents', ParentController::class)->names('parent');
});
