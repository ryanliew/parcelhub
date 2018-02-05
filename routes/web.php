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