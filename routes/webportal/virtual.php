<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\VirtualTrainingController;

Route::group(['prefix'=> 'virtual', 'cors'], function(){

    /**
     * Load all virtual coaches.
     */
    Route::get('coaches', [VirtualTrainingController::class, 'coaches']);

    /**
     * Load all upcoming session bookings.
     *
     */
    Route::get('bookings', [VirtualTrainingController::class, 'bookings'])->middleware('auth:api');

    /**
     * Load a single trainer profile.
     */
    Route::get('coaches/{user}', [VirtualTrainingController::class, 'trainerProfile']);

    /**
     * Load timeslots for booking session.
     */
    Route::get('coaches/{user}/slots', [VirtualTrainingController::class, 'slots']);

    /**
     * Load the availability calendar for a trainer.
     */
    Route::post('coaches/{user}/availability-calendar', [VirtualTrainingController::class, 'availabilityCalendar']);

    /**
     * Load a member package.
     */
    Route::get('package/{package}', [VirtualTrainingController::class, 'singleMemberPackage']);

    /**
     * Load a session.
     */
    Route::get('session/{session}', [VirtualTrainingController::class, 'singleSession'])->middleware('auth:api');

    /**
     * Book a session.
     */
    Route::post('session/book', [VirtualTrainingController::class, 'bookSession'])->middleware('auth:api');
    Route::post('session/{session}/feedback', [VirtualTrainingController::class, 'storeFeedback'])->middleware('auth:api');
    Route::delete('session/{session}', [VirtualTrainingController::class, 'cancelSession'])->middleware('auth:api');

    Route::post('cart', [VirtualTrainingController::class, 'newCart'])->middleware('auth:api');
    Route::post('purchase/{cart}', [VirtualTrainingController::class, 'purchase'])->middleware('auth:api');
});
