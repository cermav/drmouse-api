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
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');

Route::get('doctors', 'Api\DoctorController@index');
Route::get('all-doctors', 'Api\DoctorController@showAll');
Route::get('doctors/{id}', 'Api\DoctorController@show');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');
Route::get('doctors-search', 'Api\DoctorController@search');
Route::post('doctors', 'Api\DoctorController@store');
Route::post('doctor-suggestion', 'Api\DoctorSuggestionController@store');

Route::get('pets', 'Api\PetController@showall');
Route::get('pets/{id}', 'Api\PetController@showById');
Route::post('pets', 'Api\PetController@store');

Route::get('members/{mail}', 'Api\MemberController@showByEmail');
Route::post('members', 'Api\MemberController@store');

// score
Route::put('score/{id}', 'Api\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'Api\ScoreController@index');
Route::get('score/{id}', 'Api\ScoreController@show');
Route::post('score', 'Api\ScoreController@store');
Route::post('vote', 'Api\ScoreVoteController@store');

Route::post('auth/login', 'Api\AuthController@login');
Route::post('auth/google', 'Api\AuthController@google');
Route::post('auth/facebook', 'Api\AuthController@facebook');
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


Route::get('test', 'Api\NewsletterUserController@test');


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

    Route::get('all-pets', 'Api\PetController@showAll');

    // My Pet
    Route::prefix('pets')->group(function () {
        Route::get('/list', 'Api\PetController@index');
        Route::get('/latest', 'Api\PetController@latest');
        Route::post('/store', 'Api\PetController@store');
        Route::prefix('/{id}')->group(function () {
            Route::get('', 'Api\PetController@detail')->where('id', '[0-9]+');
            Route::put('/update', 'Api\PetController@update');
            Route::put('/avatar', 'Api\PetController@avatar');
            Route::put('/background', 'Api\PetController@background');
            Route::delete('/remove', 'Api\PetController@remove')->where(
                'id',
                '[0-9]+'
            );
        });
        // Appointments
        Route::get('/appointments-all', 'Api\AppointmentController@showAll');
        Route::get(
            '/{pet_id}/appointments-list',
            'Api\AppointmentController@index'
        );
        Route::prefix('/{pet_id}/appointment/')->group(function () {
            Route::get('{term_id}', 'Api\AppointmentController@detail')
            ->where('term_id','[0-9]+');
            Route::put('{term_id}/update', 'Api\AppointmentController@update');
            Route::post('store', 'Api\AppointmentController@store');
            Route::delete('{term_id}/remove','Api\AppointmentController@remove');
        });
        // Records
        Route::prefix('/{pet_id}/records')->group(function () {
            Route::get('', 'Api\PetController@get_records');
            Route::post('/store', 'Api\PetController@add_record');
            Route::put('/{record_id}/update', 'Api\PetController@update_record');
            Route::delete('/{record_id}/remove','Api\PetController@remove_record');
            // Files
            Route::get('/{record_id}/files', 'Api\PetController@get_files');
            Route::get('/{record_id}/download/{file_name}', 'Api\PetController@get_file');
            Route::put('/{record_id}/file-rename/{file_id}', 'Api\PetController@rename_file');
            Route::delete('/{record_id}/delete/{file_id}','Api\PetController@remove_file');
            Route::post('/store/{record_id}/files', 'Api\PetController@add_files');
        });
    });

    // favorite vets
    Route::get(
        'vets/{user_id}/favorite_vets',
        'Api\PetController@user_has_doctors'
    );
    Route::post(
        'vets/{user_id}/favorite_vets/{vet_id}',
        'Api\PetController@add_favorite_doctor'
    );
    Route::delete(
        'vets/{user_id}/favorite_vets/{vet_id}',
        'Api\PetController@remove_favorite_doctor'
    );
    // Vaccines
    Route::get('vaccine/{pet_id}/vaccines', 'Api\VaccineController@index');
    Route::get(
        'vaccine/{pet_id}/vaccines/{vaccine_id}',
        'Api\VaccineController@detail'
    );
    Route::get('vaccines/vaccines-list', 'Api\VaccineController@list');
    Route::get('all-vaccines', 'Api\VaccineController@showAll');
    Route::post('vaccine/{pet_id}/store', 'Api\VaccineController@store');
    Route::put(
        'vaccine/{pet_id}/update/{vac_id}',
        'Api\VaccineController@update'
    );
    Route::put(
        'vaccine/{pet_id}/setSeen/{vac_id}',
        'Api\VaccineController@setSeen'
    );
    Route::delete(
        'vaccine/{pet_id}/remove/{vac_id}',
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
