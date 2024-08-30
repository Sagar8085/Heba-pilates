<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\UserController;
use App\Http\Controllers\WebPortal\UserWorkoutDataController;
use App\Http\Controllers\WebPortal\NotificationController;
use App\Http\Controllers\WebPortal\CreditPackController;

Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::post('reset-password', [UserController::class, 'resetPassword']);

Route::group(['prefix'=> 'account', 'cors', 'middleware' => 'auth:api'], function(){

    Route::post('bacs/update', [UserController::class, 'updateBacsDetails']);

    Route::delete('/', [UserController::class, 'deleteAccount']);

    Route::post('/subscription/stripe/bacs-checkout', [UserController::class, 'generateStripeBacsCheckout']);
    Route::get('/membership/paused', [UserController::class, 'loadPausedMembership']);
    Route::post('/membership/pause', [UserController::class, 'pauseMembership']);
    Route::post('/membership/unpause', [UserController::class, 'unpauseMembership']);
    Route::post('/membership/{tier}/purchase', [UserController::class, 'storeMembership']);
    Route::post('/credit-packs/{creditPack}/checkout', [CreditPackController::class, 'generateCheckout']);
    Route::post('credit-packs/{creditPack}/purchase', [CreditPackController::class, 'storePurchase']);

    Route::patch('subscription/cancel', [UserController::class, 'cancelSubscription']);

    Route::get('qr', [UserController::class, 'viewQRCode']);

    /**
     * Load the credit packs a user has purchased.
     */
    Route::get('my-credit-packs', [CreditPackController::class, 'myPurchases']);

    /**
     * Update a users name.
     */
    Route::patch('name', [UserController::class, 'updateName']);

    /**
     * Update a users phone.
     */
    Route::patch('phone', [UserController::class, 'updatePhone']);
    Route::get('phone/check', [UserController::class, 'checkPhone']);

    /**
     * Update a users email address.
     */
    Route::patch('email', [UserController::class, 'updateEmail']);

    /**
     * Update a users password.
     */
    Route::patch('password', [UserController::class, 'updatePassword']);

    Route::post('fitness', [UserController::class, 'updateFitness']);

    /**
     * Store the user's workout data
     */
     Route::post('workout/data/store', [UserWorkoutDataController::class, 'store']);

    /**
     * New subscription purchase.
     */
    Route::post('subscription/{tier}/purchase', [UserController::class, 'storeSubscription']);

    Route::get('notifications', [NotificationController::class, 'all']);

    Route::post('logout/all-devices', [UserController::class, 'resetApiToken']);

    Route::get('notification-preferences', [UserController::class, 'notificationPreferences']);
    Route::post('notification-preferences', [UserController::class, 'updateNotificationPreferences']);

    Route::get('content-preferences', [UserController::class, 'contentPreferences']);
    Route::post('content-preferences', [UserController::class, 'updateContentPreferences']);
});
