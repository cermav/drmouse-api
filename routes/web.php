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
Route::get('/faq', 'FAQController@addDoctor')->name('faq');
Route::get('/contact', 'StaticController@showContact')->name('contact');
Route::get('/get-properties', 'PropertyController@getPropertyByName')->name('get-properties');
Route::get('/get-services', 'ServiceController@getServiceByName')->name('get-services');
Route::get('/pridat-veterinarni-ordinaci', 'DoctorController@addDoctor')->name('add-doctor');
Route::post('/create-doctor', 'DoctorController@createDoctor')->name('create-doctor');


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
