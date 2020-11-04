<?php

use Illuminate\Http\Request;
use app\Http\API\DoctorController;

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

Auth::routes(['verify' => true]);

/* Api for web */
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');

Route::get('doctors', 'Api\DoctorController@index');
//Route::get('all-doctors', 'Api\DoctorController@showAll');
Route::get('all-doctors', [DoctorController::class, 'showAll']);
Route::get('doctors/{id}', 'Api\DoctorController@show');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');
Route::post('doctors', 'Api\DoctorController@store');
Route::post('doctor-suggestion', 'Api\DoctorSuggestionController@store');

Route::get('pets', 'Api\PetsController@showall');
Route::get('pets/{id}', 'Api\PetsController@showById');
Route::post('pets', 'Api\PetsController@store');

Route::post('members', 'Api\MemberController@store');

// score
Route::put('score/{id}', 'Api\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'Api\ScoreController@index');
Route::get('score/{id}', 'Api\ScoreController@show');
Route::post('score', 'Api\ScoreController@store');
Route::post('vote', 'Api\ScoreVoteController@store');

Route::post('auth/login', 'Api\AuthController@login');
Route::post('auth/forgot-password', 'Api\Auth\ForgotPasswordController')->name(
    'forgot.password'
);
Route::post(
    'auth/reset-password',
    'Api\Auth\ResetPasswordController@reset'
)->name('reset.password');
Route::put(
    'auth/activation/{id}',
    'Api\Auth\ActivationController@activate'
)->name('member.activation');
Route::get('email/verify/{id}', 'Api\Auth\VerificationController@verify')->name(
    'verification.verify'
);
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::post('newsletter', 'Api\NewsletterUserController@store');
Route::get(
    'newsletter/verify/{id}',
    'Api\NewsletterUserController@verify'
)->name('newsletter.verify');

Route::group(['middleware' => ['jwt.auth']], function () {
    // auth
    Route::get('auth/info', 'Api\AuthController@info');
    Route::get('auth/refresh', 'Api\AuthController@refresh');
    Route::get('auth/logout', 'Api\AuthController@logout');
    Route::put(
        'auth/change-password/{id}',
        'Api\Auth\ChangePasswordController@update'
    );

    // doctor profile
    Route::put('doctors/{id}', 'Api\DoctorController@update');
    Route::put('opening-hours/{id}', 'Api\OpeningHoursController@update');
    Route::put('property/{id}', 'Api\PropertyController@update');
    Route::put('service/{id}', 'Api\ServiceController@update');
    Route::put('gallery/{id}', 'Api\GalleryController@update');
    Route::delete('gallery/{id}', 'Api\GalleryController@delete');

    Route::get('members/{id}', 'Api\MemberController@show');
    Route::put('members/{id}', 'Api\MemberController@update');

    // My Pet
    Route::get('pets', 'Api\PetsController@index');
    Route::get('all-pets', 'Api\PetsController@showAll');
    Route::get('pets/{id}', 'Api\PetsController@detail');
    Route::get('latest', 'Api\PetsController@latest');
    Route::post('pets', 'Api\PetsController@store');
    Route::put('pets/{id}', 'Api\PetsController@update');
    Route::delete('pets/{id}', 'Api\PetsController@remove');
    Route::put('pets/{id}/avatar', 'Api\PetsController@avatar');
    Route::put('pets/{id}/background', 'Api\PetsController@background');
    // Appointments
    Route::get('pets/{id}/appointments', 'Api\AppointmentController@index');
    Route::get('pets/appointments/', 'Api\AppointmentController@showAll');
    Route::get(
        'pets/{pet_id}/appointment/{id}',
        'Api\AppointmentController@detail'
    );
    Route::put(
        'pets/{id}/appointment/{term_id}',
        'Api\AppointmentController@update'
    );
    Route::post(
        'pets/{pet_id}/appointment/',
        'Api\AppointmentController@store'
    );
    Route::put(
        'pets/{pet_id}/appointment/{id}',
        'Api\AppointmentController@update'
    );
    Route::delete(
        'pets/{pet_id}/appointment/{id}',
        'Api\AppointmentController@remove'
    );
    //favorite vets
    Route::get('pets/{pet_id}/fav_vets', 'Api\PetsController@getVet');
    Route::post('pets/{pet_id}/fav_vets/{vet_id}', 'Api\PetsController@addVet');
    Route::delete(
        'pets/{pet_id}/fav_vets/{vet_id}',
        'Api\PetsController@deleteVet'
    );
    // Vaccines
    Route::get('pets/{pet_id}/vaccines', 'Api\VaccineController@index');
    Route::get('pets/{pet_id}/vaccines/{id}', 'Api\VaccineController@detail');
    Route::get('all-vaccines', 'Api\VaccineController@showAll');
    Route::post('pets/{pet_id}/vaccines', 'Api\VaccineController@store');
    Route::put(
        'pets/{pet_id}/vaccines/{vac_id}',
        'Api\VaccineController@update'
    );
    Route::delete(
        'pets/{pet_id}/vaccines/{vac_id}',
        'Api\VaccineController@remove'
    );

    // score
    Route::delete('score/{id}', 'Api\ScoreController@delete');
});

/* Api for mobile application */
Route::group(['prefix' => 'mobile'], function () {
    Route::apiResource('doctors', 'Api\Mobile\DoctorController');
    Route::apiResource('properties', 'Api\Mobile\PropertyController');
    Route::apiResource('score', 'Api\Mobile\ScoreController');
    Route::apiResource('score-category', 'Api\Mobile\ScoreCategoryController');
    Route::apiResource('services', 'Api\Mobile\ServiceController');
    Route::apiResource('opening-hours', 'Api\Mobile\OpeningHoursController');
});

// administration
Route::group(['prefix' => 'admin', 'middleware' => ['jwt.auth']], function () {
    Route::apiResource('members', 'Api\MemberController');
    Route::apiResource('doctors', 'Api\Admin\DoctorController');
    Route::apiResource('doctor-status', 'Api\Admin\DoctorStatusController');
    Route::apiResource('score', 'Api\Admin\ScoreController');
});
