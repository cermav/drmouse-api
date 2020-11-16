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

Auth::routes(['verify' => true]);

/* Api for web */
Route::apiResource('properties', 'API\PropertyController');
Route::apiResource('services', 'API\ServiceController');

Route::get('doctors', 'API\DoctorController@index');
Route::get('all-doctors', 'API\DoctorController@showAll');
Route::get('doctors/{id}', 'API\DoctorController@show');
Route::get('doctor-by-slug/{slug}', 'API\DoctorController@showBySlug');
Route::post('doctors', 'API\DoctorController@store');
Route::post('doctor-suggestion', 'API\DoctorSuggestionController@store');

Route::get('pets', 'API\PetsController@showall');
Route::get('pets/{id}', 'API\PetsController@showById');
Route::post('pets', 'API\PetsController@store');

Route::post('members', 'API\MemberController@store');

// score
Route::put('score/{id}', 'API\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'API\ScoreController@index');
Route::get('score/{id}', 'API\ScoreController@show');
Route::post('score', 'API\ScoreController@store');
Route::post('vote', 'API\ScoreVoteController@store');

Route::post('auth/login', 'API\AuthController@login');
Route::post('auth/forgot-password', 'API\Auth\ForgotPasswordController')->name(
    'forgot.password'
);
Route::post(
    'auth/reset-password',
    'API\Auth\ResetPasswordController@reset'
)->name('reset.password');
Route::put(
    'auth/activation/{id}',
    'API\Auth\ActivationController@activate'
)->name('member.activation');
Route::get('email/verify/{id}', 'API\Auth\VerificationController@verify')->name(
    'verification.verify'
);
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::post('newsletter', 'API\NewsletterUserController@store');
Route::get(
    'newsletter/verify/{id}',
    'API\NewsletterUserController@verify'
)->name('newsletter.verify');

Route::group(['middleware' => ['jwt.auth']], function () {
    // auth
    Route::get('auth/info', 'API\AuthController@info');
    Route::get('auth/refresh', 'API\AuthController@refresh');
    Route::get('auth/logout', 'API\AuthController@logout');
    Route::put(
        'auth/change-password/{id}',
        'API\Auth\ChangePasswordController@update'
    );

    // doctor profile
    Route::put('doctors/{id}', 'API\DoctorController@update');
    Route::put('opening-hours/{id}', 'API\OpeningHoursController@update');
    Route::put('property/{id}', 'API\PropertyController@update');
    Route::put('service/{id}', 'API\ServiceController@update');
    Route::put('gallery/{id}', 'API\GalleryController@update');
    Route::delete('gallery/{id}', 'API\GalleryController@delete');

    Route::get('members/{id}', 'API\MemberController@show');
    Route::put('members/{id}', 'API\MemberController@update');

    // My Pet
    Route::get('pets/list', 'API\PetController@index');
    Route::get('all-pets', 'API\PetController@showAll');
    Route::get('pets/{id}', 'API\PetController@detail')->where('id', '[0-9]+');
    Route::get('pets/latest', 'API\PetController@latest');
    Route::post('pets/store', 'API\PetController@store');
    Route::put('pets/{id}/update', 'API\PetController@update');
    Route::put('pets/{id}/avatar', 'API\PetController@avatar');
    Route::put('pets/{id}/background', 'API\PetController@background');
    Route::delete('pets/{id}/remove', 'API\PetController@remove');
    // Appointments
    Route::get(
        'pets/{pet_id}/appointments/list',
        'API\AppointmentController@index'
    );
    Route::get('pets/appointments-all', 'API\AppointmentController@showAll');
    Route::get(
        'pets/{pet_id}/appointment/{term_id}',
        'API\AppointmentController@detail'
    )->where('term_id', '[0-9]+');
    Route::put(
        'pets/{pet_id}/appointment/{term_id}/update',
        'API\AppointmentController@update'
    );
    Route::post(
        'pets/{pet_id}/appointment/store',
        'API\AppointmentController@store'
    );
    Route::delete(
        'pets/{pet_id}/appointment/{term_id}/remove',
        'API\AppointmentController@remove'
    );
    //favorite vets TODO
    /*
    Route::get('pets/{pet_id}/fav_vets', 'API\PetsController@getVets');
    Route::post('pets/{pet_id}/fav_vets/{vet_id}', 'API\PetsController@addVet');
    Route::delete(
        'pets/{pet_id}/fav_vets/{vet_id}',
        'API\PetsController@deleteVet'
    );
    // Vaccines
    Route::get('pets/{pet_id}/vaccines', 'API\VaccineController@index');
    Route::get('pets/{pet_id}/vaccines/{vac_id}', 'API\VaccineController@detail');
    Route::get('all-vaccines', 'API\VaccineController@showAll');
    Route::post('pets/{pet_id}/vaccines', 'API\VaccineController@store');
    Route::put(
        'pets/{pet_id}/vaccines/{vac_id}',
        'API\VaccineController@update'
    );
    Route::delete(
        'pets/{pet_id}/vaccines/{vac_id}',
        'API\VaccineController@remove'
    );
*/
    // score
    Route::delete('score/{id}', 'API\ScoreController@delete');
});

/* Api for mobile application */
Route::group(['prefix' => 'mobile'], function () {
    Route::apiResource('doctors', 'API\Mobile\DoctorController');
    Route::apiResource('properties', 'API\Mobile\PropertyController');
    Route::apiResource('score', 'API\Mobile\ScoreController');
    Route::apiResource('score-category', 'API\Mobile\ScoreCategoryController');
    Route::apiResource('services', 'API\Mobile\ServiceController');
    Route::apiResource('opening-hours', 'API\Mobile\OpeningHoursController');
});

// administration
Route::group(['prefix' => 'admin', 'middleware' => ['jwt.auth']], function () {
    Route::apiResource('members', 'API\MemberController');
    Route::apiResource('doctors', 'API\Admin\DoctorController');
    Route::apiResource('doctor-status', 'API\Admin\DoctorStatusController');
    Route::apiResource('score', 'API\Admin\ScoreController');
});
