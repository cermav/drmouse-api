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
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');

Route::apiResource('doctors', 'Api\DoctorController');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');
Route::get('all-doctors', 'Api\DoctorController@showAll');

// score
Route::put('score/{id}', 'Api\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'Api\ScoreController@index');
Route::get('score/{id}', 'Api\ScoreController@show');
Route::post('score', 'Api\ScoreController@store');
Route::post('vote', 'Api\ScoreVoteController@store');

Route::post('/register', 'Api\AuthController@register');
Route::post('auth/login', 'Api\AuthController@login');

Route::group(['middleware' => ['jwt.auth']], function() {

    // auth
    Route::get('auth/refresh', 'Api\AuthController@refresh');
    Route::get('auth/logout', 'Api\AuthController@logout');

    // score
    Route::delete('score/{id}', 'Api\ScoreController@delete');

});


/* Api for mobile application */
Route::group(['prefix' => 'mobile'], function() {
    Route::apiResource('doctors', 'Api\Mobile\DoctorController');
    Route::apiResource('properties', 'Api\Mobile\PropertyController');
    Route::apiResource('score', 'Api\Mobile\ScoreController');
    Route::apiResource('services', 'Api\Mobile\ServiceController');
});




