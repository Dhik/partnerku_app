<?php

use Illuminate\Support\Facades\Route;
use App\Domain\OtherSpent\Controllers\OtherSpentController;

/*
|--------------------------------------------------------------------------
| Other Spent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware('auth')
        ->group(function () {
            Route::prefix('otherSpent')->group(function () {
                Route::get('/', [OtherSpentController::class, 'index'])->name('otherSpent.index');
                Route::get('/data', [OtherSpentController::class, 'data'])->name('otherSpent.data');
                Route::get('/create', [OtherSpentController::class, 'create'])->name('otherSpent.create');
                Route::post('/', [OtherSpentController::class, 'store'])->name('otherSpent.store');
                Route::get('/{otherSpent}', [OtherSpentController::class, 'show'])->name('otherSpent.show');
                Route::get('/{otherSpent}/edit', [OtherSpentController::class, 'edit'])->name('otherSpent.edit');
                Route::put('/{otherSpent}', [OtherSpentController::class, 'update'])->name('otherSpent.update');
                Route::delete('/{otherSpent}', [OtherSpentController::class, 'destroy'])->name('otherSpent.destroy');
        });
});