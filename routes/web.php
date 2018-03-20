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

    Route::group(['prefix' => 'sync'], function () {
        Route::get('/', 'CollectionSyncController@index')->name('sync.index');
    });
    Route::resource('collection', 'CollectionController')->only(['index', 'show']);

    Route::get('settings', 'SettingsController@edit')->name('settings.edit');
    Route::put('settings', 'SettingsController@update')->name('settings.update');

    Route::group(['prefix' => 'lastfm'], function () {
        Route::get('/', 'LastFmWebAuthController@index')->name('lastfm.auth.index');
        Route::get('connect', 'LastFmWebAuthController@connect')->name('lastfm.auth.connect');
        Route::get('disconnect', 'LastFmWebAuthController@disconnect')->name('lastfm.auth.disconnect');
        Route::post('scrobble/{release}', 'ScrobbleController@index')->name('lastfm.scrobble');
    });

    Route::get('queue', 'ScrobbleController@showQueue')->name('lastfm.scrobble.queue');
    Route::get('clear-queue', 'ScrobbleController@clearQueue')->name('lastfm.scrobble.queue.clear');
});
