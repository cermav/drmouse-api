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

Auth::routes(['verify'=>true]);

/* Api for web */
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');

Route::get('doctors', 'Api\DoctorController@index');
Route::get('all-doctors', 'Api\DoctorController@showAll');
Route::get('doctors/{id}', 'Api\DoctorController@show');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');
Route::post('doctors', 'Api\DoctorController@store');


// score
Route::put('score/{id}', 'Api\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'Api\ScoreController@index');
Route::get('score/{id}', 'Api\ScoreController@show');
Route::post('score', 'Api\ScoreController@store');
Route::post('vote', 'Api\ScoreVoteController@store');
Route::get('score-waiting', 'Api\ScoreController@waiting');

Route::post('auth/login', 'Api\AuthController@login');
Route::post('auth/forgot-password', 'Api\Auth\ForgotPasswordController')->name('forgot.password');
Route::post('auth/reset-password', 'Api\Auth\ResetPasswordController@reset')->name('reset.password');
Route::get('email/verify/{id}', 'Api\Auth\VerificationController@verify')->name('verification.verify');
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::group(['middleware' => ['jwt.auth']], function() {

    // auth
    Route::get('auth/refresh', 'Api\AuthController@refresh');
    Route::get('auth/logout', 'Api\AuthController@logout');
    Route::put('auth/change-password/{id}', 'Api\Auth\ChangePasswordController@update');

    // doctor profile
    Route::put('doctors/{id}', 'Api\DoctorController@update');
    Route::put('opening-hours/{id}', 'Api\OpeningHoursController@update');
    Route::put('property/{id}', 'Api\PropertyController@update');
    Route::put('service/{id}', 'Api\ServiceController@update');
    Route::put('gallery/{id}', 'Api\GalleryController@update');


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




