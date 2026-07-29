<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Models\Permission;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('admin/modules', ModuleController::class)->names('admin.modules');
});
Route::get('as', function () {
    $permissions = Permission::all();

    Auth::user()->givePermissionTo($permissions);
});


require __DIR__ . '/settings.php';
