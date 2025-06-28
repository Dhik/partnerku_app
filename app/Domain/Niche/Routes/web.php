<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Niche\Controllers\NicheController;

/*
|--------------------------------------------------------------------------
| Niche Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware('auth')
    ->group(function () {
        Route::prefix('niche')
            ->group(function () {
                Route::get('/', [NicheController::class, 'index'])->name('niche.index');
                Route::get('/data', [NicheController::class, 'data'])->name('niche.data');
                Route::get('/create', [NicheController::class, 'create'])->name('niche.create');
                Route::post('/', [NicheController::class, 'store'])->name('niche.store');
                Route::get('/{niche}', [NicheController::class, 'show'])->name('niche.show');
                Route::get('/{niche}/edit', [NicheController::class, 'edit'])->name('niche.edit');
                Route::put('/{niche}', [NicheController::class, 'update'])->name('niche.update');
                Route::delete('/{niche}', [NicheController::class, 'destroy'])->name('niche.destroy');
            });
    });