<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Authentication route
 */
Route::post('authentication', 'Api\AuthenticateController@authenticate')->name('api.auth.login');
Route::resource('authentication', 'Api\AuthenticateController', ["only" => ["index"], "names" => [
    "index" => "api.auth.index"
]]);

/**
 * User model route
 */
Route::patch('users/{id}/password', 'Api\UserController@updatePassword')->name('api.user.password.update');
Route::resource('users', 'Api\UserController', ["only" => ["store", "show", "update"], "names" => [
    "store" => "api.user.store",
    "show" => "api.user.show",
    "update" => "api.user.update"
]]);

/**
 * Event model route
 */
Route::post('events/{id}/join', 'Api\EventController@joinEvent')->name('api.event.join');
Route::post('events/{id}/leave', 'Api\EventController@leaveEvent')->name('api.event.leave');
Route::post('events/{id}/cancel', 'Api\EventController@cancelEvent')->name('api.event.cancel');
Route::get('events/types', 'Api\EventController@getTypes')->name('api.event.types');
Route::resource('events', 'Api\EventController', ["only" => ["index", "store", "show", "update"], "names" => [
    "index" => "api.event.index",
    "store" => "api.event.store",
    "show" => "api.event.show",
    "update" => "api.event.update"
]]);

/**
 * Image resource route
 */
Route::resource('images', 'Api\ImageController', ["only" => ["index", "store"], "name" => [
    "index" => "api.image.index",
    "store" => "api.image.store"
]]);

