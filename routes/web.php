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
    return view('welcome');
});

Route::get('/login', 'Auth\LoginController@showLoginForm')->name('auth.show-login');
Route::post('/login', 'Auth\LoginController@login')->name('auth.login');
Route::post('/logout', 'Auth\LoginController@logout')->name('auth.logout');

Route::post('/register', 'Auth\RegisterController@store')->name('register.store');


/* Admin Area */
Route::middleware(['admin'])->group(function() {
	Route::get('/admin', 'AdminController@show')->name('admin.show');

	Route::post('/admin/events', 'EventController@store')->name('admin.event-store');
	Route::get('/admin/events', 'EventController@index')->name('admin.events');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
