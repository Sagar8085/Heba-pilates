<?php

use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Admin\Member\CreditPackController;
use App\Http\Controllers\Admin\MembershipController;

Route::group(['prefix' => 'gyms'], function() {
    Route::post('reservations', [GymController::class, 'allReservations']);
    Route::post('reservations/upcoming', [GymController::class, 'upcomingReservations']);
    Route::get('reservations/{booking}', [GymController::class, 'singleReservation']);
    Route::delete('reservations/{booking}', [GymController::class, 'deleteReservation']);

    Route::get('', [GymController::class, 'index']);
    Route::get('{gym}', [GymController::class, 'single']);
    Route::get('{gym}/machine-reservations', [GymController::class, 'machineReservations']);
    Route::get('{gym}/machine/{machine}/reservations', [GymController::class, 'singleMachineReservations']);
    Route::post('{gym}/machine/{machine}/block/{startDatetime}/{endDatetime}', [GymController::class, 'blockMachine']);
    Route::post('{gym}/machine/{machine}/free/{startDatetime}/{endDatetime}', [GymController::class, 'freeMachine']);

    /* -- Store opening times */

    Route::patch('opening-times/{gym}', [GymController::class, 'opening']);
    Route::patch('{gym}/custom-opening-times/save', [GymController::class, 'openingCustomSave']);
    Route::patch('{gym}/custom-opening-times/delete', [GymController::class, 'openingCustomDelete']);
    
    Route::get('{gym}/credit-packs', [CreditPackController::class, 'allPacksForGym']);
    Route::get('{gym}/subscription-tiers', [MembershipController::class, 'allTiersForGym']);
});
