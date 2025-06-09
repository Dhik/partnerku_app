<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Income\Controllers\IncomeController;

/*
|--------------------------------------------------------------------------
| Income Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware('auth')
        ->group(function () {
            Route::prefix('income')
            ->group(function () {
                Route::get('/', [IncomeController::class, 'index'])->name('income.index');
                Route::get('/data', [IncomeController::class, 'data'])->name('income.data');
                Route::get('/users', [IncomeController::class, 'getUsers'])->name('income.users');
                Route::get('/create', [IncomeController::class, 'create'])->name('income.create');
                Route::post('/', [IncomeController::class, 'store'])->name('income.store');
                Route::get('/{income}', [IncomeController::class, 'show'])->name('income.show');
                Route::get('/{income}/edit', [IncomeController::class, 'edit'])->name('income.edit');
                Route::put('/{income}', [IncomeController::class, 'update'])->name('income.update');
                Route::delete('/{income}', [IncomeController::class, 'destroy'])->name('income.destroy');
            });
});