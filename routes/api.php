<?php

use App\Http\Controllers\AwsSnsController;
use App\Http\Controllers\BulletDigitalController;
use App\Http\Controllers\DoorAccessController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\WebPortal\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('v1/users', 'Api\UserController')->name('api.users.store');

Route::post('webhooks/stripe/payment-intent', [StripeController::class, 'paymentIntent']);
Route::post('webhooks/bullet-digital/onboard/member', [BulletDigitalController::class, 'onboardMember']);
Route::post('webhooks/bullet-digital/promo/unlimited', [BulletDigitalController::class, 'unlimitedPromo']);

Route::match(['GET', 'POST'], '/aws-sns/transcoder/complete', [AwsSnsController::class, 'transcoderComplete']);
Route::post('webhook/dooraccess/scan', [DoorAccessController::class, 'turnstileScanned']);

Route::post('stripe/webhooks', [StripeController::class, 'incomingWebhook']);

Route::post('payment-confirmation', [StripeController::class, 'paymentConfirmation']);

Route::group(['prefix' => 'user', 'middleware' => 'auth:api', 'cors'], function() {
    Route::get('/', function (Request $request) {
        return $request->user()->load('details', 'subscription', 'pendingSessions', 'latestNotifications');
    });

    Route::get('intro-pack/available', [UserController::class, 'canPurchaseIntroPack']);

    Route::get('/get', function (Request $request) {
        return response()->json(auth()->user()->load('details', 'subscription'));
    });

    Route::post('details/heightweight', [UserController::class, 'updateHeightWeight']);


    Route::post('custom-avatar', [UserController::class, 'uploadCustomAvatar']);
    Route::post('placeholder-avatar', [UserController::class, 'uploadPlaceholderAvatar']);
});

Route::any('send-email', [SendEmailController::class, 'index']);
// Load Web portal specific apis
require_once('webportal.php');

// Load mobile app specific apis.
require_once('app.php');

// Load admin specific apis.
require('admin.php');

// Load admin specific apis.
require_once('auth.php');

// Load zoom specific apis.
require_once('zoom.php');

// Load tablet specific apis.
require_once('tablet.php');
