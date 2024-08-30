<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\Auth\WebPortalAuthController;
use App\Http\Controllers\WebPortal\SearchController;
use App\Http\Controllers\WebPortal\GymController;
use App\Http\Controllers\WebPortal\CreditPackController;
use App\Http\Controllers\WebPortal\UserController;
use App\Http\Controllers\WebPortal\HelpController;
use App\Http\Controllers\WebPortal\ContactController;
use App\Http\Controllers\WebPortal\Auth\RegistrationController;
use App\Http\Controllers\SendEmailController;

Route::get('content-preferences', [WebPortalAuthController::class, 'contentPreferences']);
Route::patch('content-preferences', [WebPortalAuthController::class, 'updateContentPreferences']);

// Route::post('register/{step?}', [WebPortalAuthController::class, 'register']);
Route::post('register', [RegistrationController::class, 'register']);

Route::group(['prefix' => 'onboarding', 'middleware' => 'auth:api', 'cors'], function () {
    Route::post('parq', [RegistrationController::class, 'savePARQResponse']);

    Route::get('goals', [RegistrationController::class, 'getGoals']);
    Route::post('goals', [RegistrationController::class, 'setGoals']);
    Route::get('focuses', [RegistrationController::class, 'getFocuses']);
    Route::post('focuses', [RegistrationController::class, 'setFocuses']);
    Route::get('body-part-focuses', [RegistrationController::class, 'getBodyPartFocuses']);
    Route::post('body-part-focuses', [RegistrationController::class, 'setBodyPartFocuses']);
    Route::post('fitness-level', [RegistrationController::class, 'setFitnessLevel']);
    Route::post('pilates-experience', [RegistrationController::class, 'setPilatesExperience']);
    Route::post('parq', [RegistrationController::class, 'savePARQResponse']);
});

Route::get('awsbucketurl', function(){
    return getenv('AWS_BUCKET_URL');
});


// Load ondemand specific apis.
require_once('webportal/ondemand.php');

require_once('webportal/workouts.php');

require_once('webportal/podcasts.php');

require_once('webportal/virtual.php');

require_once('webportal/blogs.php');

require_once('webportal/vlogs.php');

require_once('webportal/account.php');

require_once('webportal/custom_workouts.php');

require_once('webportal/exercises.php');

require_once('webportal/courses.php');

require_once('webportal/live.php');

Route::post('user/scan-tablet-qr', [UserController::class, 'scanTabletQR'])->middleware('auth:api');

Route::get('gyms', [GymController::class, 'index']);
Route::get('gyms/{gym}', [GymController::class, 'single']);
Route::get('gyms/{gym}/reservations/dates', [GymController::class, 'reservationDates'])->middleware('auth:api');
Route::get('gyms/{gym}/reservations/timeslots', [GymController::class, 'reservationTimeslots']);
Route::post('gyms/{gym}/reservations/timeslots', [GymController::class, 'createReservation'])->middleware('auth:api');

/* -- Get Custom opening times */
Route::get('gyms/{gym}/custom-opening-times', [GymController::class, 'openingCustom']);

Route::delete('reservations/{reservation}', [GymController::class, 'cancelReservation'])->middleware('auth:api');

Route::get('credit-packs', [CreditPackController::class, 'index'])->middleware('auth:api');

Route::get('search', [SearchController::class, 'index']);

Route::get('my/reservations', [UserController::class, 'myReservations'])->middleware('auth:api');

Route::get('help', [HelpController::class, 'index']);
Route::get('help/categories', [HelpController::class, 'categories']);
Route::get('help/featured', [HelpController::class, 'featured']);
Route::post('help/search', [HelpController::class, 'search']);
Route::get('help/article/{article:slug}', [HelpController::class, 'single']);
Route::get('help/category/{category:slug}', [HelpController::class, 'singleCategory']);

Route::post('contact', [ContactController::class, 'sendEmail']);

Route::any('send-email', [SendEmailController::class, 'index']);


// Route::any('send-email', [ContactController::class, 'sendEmail']);
