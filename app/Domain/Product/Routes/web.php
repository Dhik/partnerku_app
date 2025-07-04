<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Product\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware('auth')
    ->group(function () {
        Route::prefix('product')
            ->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('product.index');
                Route::get('/data', [ProductController::class, 'data'])->name('product.data');
                Route::get('/create', [ProductController::class, 'create'])->name('product.create');
                Route::post('/', [ProductController::class, 'store'])->name('product.store');
                Route::get('/{product}', [ProductController::class, 'show'])->name('product.show');
                Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
                Route::put('/{product}', [ProductController::class, 'update'])->name('product.update');
                Route::delete('/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
            });
    });