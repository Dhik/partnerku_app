<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Payroll\Controllers\PayrollController;

/*
|--------------------------------------------------------------------------
| Payroll Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware('auth')
        ->group(function () {
            Route::prefix('payroll')
            ->group(function () {
                Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
                Route::get('/data', [PayrollController::class, 'data'])->name('payroll.data');
                Route::get('/create', [PayrollController::class, 'create'])->name('payroll.create');
                Route::post('/', [PayrollController::class, 'store'])->name('payroll.store');
                Route::get('/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
                Route::get('/{payroll}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
                Route::put('/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
                Route::delete('/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
            });
});