<?php

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

Route::group(['namespace' => 'Auth'], function () {
    Route::get('login', 'LoginController@login')->name('login');
    Route::post('authenticate', 'LoginController@authenticate')->name('authenticate');
    Route::post('logout', 'LoginController@logout')->name('logout');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'CollectionController@index');
    Route::resource('collection', 'CollectionController')->only(['index', 'show']);
    Route::resource('settings', 'SettingsController')->only(['index']);
});
