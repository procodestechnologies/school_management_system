<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('admin/modules', ModuleController::class)->names('admin.modules');
});


require __DIR__ . '/settings.php';
