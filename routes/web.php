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

Route::get('/', function () {
    return view('bootstrap');
});

/**
 * Uploads
 */
Route::group(["prefix" => "upload"], function() {
    // Event Images
    Route::get('images/event/{filename}', 'Api\ImageController@getEventImage')->name('image.event.get');
});
