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

/* Scores */
Route::apiResource('scores', 'Api\ScoreController');

/* Doctors */
Route::apiResource('doctors', 'Api\DoctorController');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');


/* Api for mobile application */
Route::group(['prefix' => 'mobile'], function() {
    Route::apiResource('doctors', 'Api\Mobile\DoctorController');
    Route::apiResource('properties', 'Api\Mobile\PropertyController');
    Route::apiResource('score', 'Api\Mobile\ScoreController');
    Route::apiResource('services', 'Api\Mobile\ServiceController');
});




