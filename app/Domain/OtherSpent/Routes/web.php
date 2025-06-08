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

Route::prefix('otherSpent')
    //->middleware('auth')
    ->group(function () {

        Route::get('/', 'OtherSpentController@index')->name('otherSpent.index');
        Route::get('/create', 'OtherSpentController@create')->name('otherSpent.create');
        Route::post('/', 'OtherSpentController@store')->name('otherSpent.store');
        Route::get('/{otherSpent}', 'OtherSpentController@show')->name('otherSpent.show');
        Route::get('/{otherSpent}/edit', 'OtherSpentController@edit')->name('otherSpent.edit');
        Route::put('/{otherSpent}', 'OtherSpentController@update')->name('otherSpent.update');
        Route::delete('{otherSpent}', 'OtherSpentController@destroy')->name('otherSpent.destroy');
    });
