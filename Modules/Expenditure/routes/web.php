<?php

use Illuminate\Support\Facades\Route;
use Modules\Expenditure\Http\Controllers\ExpenditureCategoryController;
use Modules\Expenditure\Http\Controllers\ExpenditureController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Declared before the resource so "expenditures/categories" isn't
    // swallowed by the {expenditure} show route.
    Route::get('expenditures/categories', [ExpenditureCategoryController::class, 'index'])->name('expenditure.categories.index');
    Route::post('expenditures/categories', [ExpenditureCategoryController::class, 'store'])->name('expenditure.categories.store');
    Route::post('expenditures/categories/defaults', [ExpenditureCategoryController::class, 'loadDefaults'])->name('expenditure.categories.defaults');
    Route::put('expenditures/categories/{category}', [ExpenditureCategoryController::class, 'update'])->name('expenditure.categories.update');
    Route::delete('expenditures/categories/{category}', [ExpenditureCategoryController::class, 'destroy'])->name('expenditure.categories.destroy');

    Route::resource('expenditures', ExpenditureController::class)->names('expenditure');
});
