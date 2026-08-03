<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevicesController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SyncStudentToDeviceController;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\HasInstitution;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
// make sure to add the EnsureAccountIsActive middleware to all routes

Route::middleware(['auth', 'verified',  HasInstitution::class])->prefix('dashboard')->group(function () {
    Route::get('', DashboardController::class)->name('dashboard');
    // store/show aren't implemented - device creation and viewing are
    // handled entirely by the create/edit Livewire components.
    Route::resource('/devices', DevicesController::class)->names('devices')->except(['store', 'show']);
    Route::resource('admin/modules', ModuleController::class)->names('admin.modules');
});
Route::get('/students/{studentId}/sync-device', [SyncStudentToDeviceController::class, 'syncStudent']);
require __DIR__ . '/settings.php';
