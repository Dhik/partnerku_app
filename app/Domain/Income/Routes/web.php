<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('income')
    //->middleware('auth')
    ->group(function () {

        Route::get('/', 'IncomeController@index')->name('income.index');
        Route::get('/create', 'IncomeController@create')->name('income.create');
        Route::post('/', 'IncomeController@store')->name('income.store');
        Route::get('/{income}', 'IncomeController@show')->name('income.show');
        Route::get('/{income}/edit', 'IncomeController@edit')->name('income.edit');
        Route::put('/{income}', 'IncomeController@update')->name('income.update');
        Route::delete('{income}', 'IncomeController@destroy')->name('income.destroy');
    });
