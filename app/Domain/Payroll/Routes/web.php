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

Route::prefix('payroll')
    //->middleware('auth')
    ->group(function () {

        Route::get('/', 'PayrollController@index')->name('payroll.index');
        Route::get('/create', 'PayrollController@create')->name('payroll.create');
        Route::post('/', 'PayrollController@store')->name('payroll.store');
        Route::get('/{payroll}', 'PayrollController@show')->name('payroll.show');
        Route::get('/{payroll}/edit', 'PayrollController@edit')->name('payroll.edit');
        Route::put('/{payroll}', 'PayrollController@update')->name('payroll.update');
        Route::delete('{payroll}', 'PayrollController@destroy')->name('payroll.destroy');
    });
