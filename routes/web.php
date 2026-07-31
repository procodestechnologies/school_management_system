<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SyncStudentToDeviceController;
use App\Http\Middleware\HasInstitution;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified', HasInstitution::class])->prefix('dashboard')->group(function () {
    Route::get('', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('admin/modules', ModuleController::class)->names('admin.modules');
});
Route::get('/students/{studentId}/sync-device', [SyncStudentToDeviceController::class, 'syncStudent']);
require __DIR__ . '/settings.php';
