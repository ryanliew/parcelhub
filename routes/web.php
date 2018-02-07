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

Auth::routes();

/* Socialite authenticate */
$s = 'social.';
Route::get('social/{provider}', 			['as' => $s . 'redirect', 'uses'=> 'Auth\SocialController@redirectToProvider']);
Route::get('social/{provider}/callback',  	['as' => $s . 'callback', 'uses'=> 'Auth\SocialController@handleProviderCallback']);

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'lot'], function() {
	Route::get('index', 'LotController@index');
	Route::get('delete/{id}', 'LotController@destroy');
	Route::post('store', 'LotController@store');
	Route::post('update', 'LotController@update');
});

Route::group(['prefix' => 'category'], function() {
	Route::get('index', 'CategoryController@index');
	Route::get('delete/{id}', 'CategoryController@destroy');
	Route::post('store', 'CategoryController@store');
	Route::post('update', 'CategoryController@update');
});

Route::group(['prefix' => 'product'], function() {
	Route::get('index', 'ProductController@index');
	Route::get('delete/{id}', 'ProductController@destroy');
	Route::post('store', 'ProductController@store');
	Route::post('update', 'ProductController@update');
});
