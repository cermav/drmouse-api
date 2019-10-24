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

/* Api for web */
Route::apiResource('scores', 'Api\ScoreController');
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');
Route::apiResource('doctors', 'Api\DoctorController');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');

Route::post('/register', 'Api\AuthController@register');
Route::post('auth/login', 'Api\AuthController@login');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('auth/refresh', 'Api\AuthController@refresh');
    Route::get('auth/logout', 'Api\AuthController@logout');
});


/* Api for mobile application */
Route::group(['prefix' => 'mobile'], function() {
    Route::apiResource('doctors', 'Api\Mobile\DoctorController');
    Route::apiResource('properties', 'Api\Mobile\PropertyController');
    Route::apiResource('score', 'Api\Mobile\ScoreController');
    Route::apiResource('services', 'Api\Mobile\ServiceController');
});




