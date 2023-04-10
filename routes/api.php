<?php

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

/* Api for web */
Route::apiResource('properties', 'Api\PropertyController');
Route::apiResource('services', 'Api\ServiceController');

/**
 * Doctor
 */
Route::get('doctors', 'Api\DoctorController@index');

Route::get('all-doctors', 'Api\DoctorController@showAll');
Route::get('doctors/{id}', 'Api\DoctorController@show');
Route::get('doctor-by-slug/{slug}', 'Api\DoctorController@showBySlug');
Route::get('doctors-search', 'Api\DoctorController@search');
Route::post('doctors', 'Api\DoctorController@store');
Route::post('doctor-suggestion', 'Api\DoctorSuggestionController@store');


/**
 * Pet
 */
//Route::post('pets', 'Api\PetController@store');

/**
 * Member
 */
Route::get('members/email/{mail}', 'Api\MemberController@showByEmail')->where('mail', '[A-Za-z0-9.@+-=?!*&]+');
Route::post('members', 'Api\MemberController@store');

/**
 * Score
 */
Route::put('score/{id}', 'Api\ScoreController@update'); // should be under auth, but it is not working now
Route::get('score', 'Api\ScoreController@index');
Route::get('score/{id}', 'Api\ScoreController@show');
Route::post('score', 'Api\ScoreController@store');
Route::post('vote', 'Api\ScoreVoteController@store');

/**
 * Auth
 */
Route::post('auth/login', 'Api\AuthController@login');
Route::post('auth/google', 'Api\AuthController@google');
Route::post('auth/facebook', 'Api\AuthController@facebook');
Route::post('auth/forgot-password', 'Api\Auth\ForgotPasswordController')->name(
    'forgot.password'
);
Route::post('auth/reset-password', 'Api\Auth\ResetPasswordController@reset')->name('reset.password');
Route::put('auth/activation/{id}', 'Api\Auth\ActivationController@activate')->name('member.activation');

//Route::get('test', 'Api\NewsletterUserController@test');

Route::get('email/verify/{id}', 'Api\Auth\VerificationController@verify')->name(
    'verification.verify'
);
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::post('newsletter', 'Api\NewsletterUserController@store');
Route::get(
    'newsletter/verify/{id}',
    'Api\NewsletterUserController@verify'
)->name('newsletter.verify');

/**
 * Authorized access
 */
Route::group(['middleware' => ['jwt.auth']], function () {
    /**
     * Auth
     */
    Route::get('auth/info', 'Api\AuthController@info');
    Route::get('auth/refresh', 'Api\AuthController@refresh');
    Route::get('auth/logout', 'Api\AuthController@logout');
    Route::put(
        'auth/change-password/{id}',
        'Api\Auth\ChangePasswordController@update'
    );

    Route::post('auth/google-pair', 'Api\AuthController@googleLink');
    Route::get('auth/google-unpair', 'Api\AuthController@googleUnlink');

    Route::post('auth/facebook-pair', 'Api\AuthController@facebookLink');
    Route::get('auth/facebook-unpair', 'Api\AuthController@facebookUnlink');

    Route::get('auth/user-data-deletion', 'Api\AuthController@userDataDeletion');

    /**
     * Event
     */
    Route::get('{member_id}/event/getForMember', 'Api\CalendarController@getForMember');
    Route::post('{member_id}/event/member/create', 'Api\CalendarController@createForMember');
    Route::put('{member_id}/event/member/update/{id}', 'Api\CalendarController@updateAppointmentMember');
    Route::get('{member_id}/event/member/accept/{id}', 'Api\CalendarController@memberAccept');
    Route::get('{member_id}/event/member/deny/{id}', 'Api\CalendarController@memberDeny');
    Route::delete('{member_id}/event/member/{id}', 'Api\CalendarController@memberDelete');

    Route::get('{doctor_id}/event/getForDoctor', 'Api\CalendarController@getForDoctor');
    Route::post('{doctor_id}/event/doctor/create', 'Api\CalendarController@createForDoctor');
    Route::put('{doctor_id}/event/doctor/update/{id}', 'Api\CalendarController@updateAppointmentDoctor');
    Route::get('{doctor_id}/event/doctor/accept/{id}', 'Api\CalendarController@doctorAccept');
    Route::get('{doctor_id}/event/doctor/deny/{id}', 'Api\CalendarController@doctorDeny');
    Route::delete('{doctor_id}/event/doctor/{id}', 'Api\CalendarController@doctorDelete');

    Route::post('record/{event_id}/create', 'Api\RecordController@store');

    // doctor profile
    Route::put('doctors/{id}', 'Api\DoctorController@update');
    Route::put('opening-hours/{id}', 'Api\OpeningHoursController@update');
    Route::put('property/{id}', 'Api\PropertyController@update');
    Route::put('service/{id}', 'Api\ServiceController@update');
    Route::put('gallery/{id}', 'Api\GalleryController@update');
    Route::delete('gallery/{id}', 'Api\GalleryController@delete');

    Route::get('doctor/price-list/get', 'Api\PriceChartController@index');
    Route::post('doctor/price-list/create', 'Api\PriceChartController@store');
    Route::put('doctor/price-list/update', 'Api\PriceChartController@update');
    Route::delete('doctor/price-list/delete', 'Api\PriceChartController@delete');

    Route::get('members/{id}', 'Api\MemberController@show')->where('id', '[0-9]+');
    Route::put('members/{id}', 'Api\MemberController@update')->where('id', '[0-9]+');
    // favorite vets
    Route::get('vets/{user_id}/favorite_vets', 'Api\MemberController@userHasDoctors');
    Route::post('vets/{user_id}/favorite_vets/{vet_id}', 'Api\MemberController@addFavoriteDoctor');
    Route::delete('vets/{user_id}/favorite_vets/{vet_id}', 'Api\MemberController@removeFavoriteDoctor');

    Route::get('record/list', 'Api\RecordController@getAllRecords');
    Route::post('record/{event_id}/create', 'Api\RecordController@store');

    // My Pet
    Route::prefix('pets')->group(function () {
        Route::get('/list', 'Api\PetController@index');
        Route::get('/latest', 'Api\PetController@latest');
        Route::post('/store', 'Api\PetController@store');
        Route::prefix('/{pet_id}')->group(function () {
            Route::get('', 'Api\PetController@detail')->where('pet_id', '[0-9]+');
            Route::put('/update', 'Api\PetController@update');
            Route::put('/avatar', 'Api\PetController@avatar');
            Route::put('/background', 'Api\PetController@background');
            Route::delete('/remove', 'Api\PetController@remove')->where(
                'id',
                '[0-9]+'
            );
        });
        // Appointments
        Route::get(
            '/{pet_id}/appointments-list',
            'Api\AppointmentController@index'
        );
        Route::prefix('/{pet_id}/appointment/')->group(function () {
            Route::get('{term_id}', 'Api\AppointmentController@detail')
                ->where('term_id', '[0-9]+');
            Route::put('{term_id}/update', 'Api\AppointmentController@update');
            Route::post('store', 'Api\AppointmentController@store');
            Route::delete('{term_id}/remove', 'Api\AppointmentController@remove');
        });

        // Records
        Route::prefix('/{pet_id}/records')->group(function () {
            Route::get('', 'Api\RecordController@getRecords');
            Route::post('/store', 'Api\RecordController@add_record');
            Route::put('/{record_id}/update', 'Api\RecordController@update_record');
            Route::delete('/{record_id}/remove', 'Api\RecordController@remove_record');
            // Files
            Route::get('/{record_id}/files', 'Api\RecordController@get_files');
//            Route::get('/{record_id}/download/{file_name}', 'Api\RecordController@get_file');
            Route::put('/{record_id}/file-rename/{file_id}', 'Api\RecordController@rename_file');
            Route::delete('/{record_id}/delete/{file_id}', 'Api\RecordController@remove_file');
            Route::post('/store/{record_id}/files', 'Api\RecordController@add_files');
        });
    });

    // Vaccines
    Route::get('vaccine/{pet_id}/vaccines', 'Api\VaccineController@index');
    Route::get(
        'vaccine/{pet_id}/vaccines/{vaccine_id}',
        'Api\VaccineController@detail'
    );
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
    Route::apiResource('members', 'Api\Mobile\MemberController');

    Route::apiResource('auth/login', 'Api\AuthController@login');
    Route::apiResource('auth/google', 'Api\AuthController@google');
    Route::apiResource('auth/facebook', 'Api\AuthController@facebook');

    Route::group(['middleware' => ['jwt.auth']], function () {
        Route::apiResource('score-vote', 'Api\Mobile\ScoreVoteController');
    });
});

// administration
Route::group(['prefix' => 'admin',
              'middleware' => ['jwt.auth']], function () {
    Route::apiResource('members', 'Api\MemberController');
    Route::apiResource('doctors', 'Api\Admin\DoctorController');
    Route::apiResource('doctor-status', 'Api\Admin\DoctorStatusController');
    Route::apiResource('score', 'Api\Admin\ScoreController');
    // TODO URI?
    Route::get('all-pets', 'Api\Admin\PetController@showAll');
    Route::get('{owner_id}/event/getAll', 'Api\Admin\CalendarController@showAll');
    Route::get('/appointments-all', 'Api\Admin\CalendarController@showAll');
    Route::get('all-vaccines', 'Api\Admin\VaccineController@showAll');
});
