<?php

use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\TrainerSpecialisationController;

Route::post('/trainers/{user}/availability', [AvailabilityController::class, 'availability']);
Route::post('/trainers/{user}/availability/save', [AvailabilityController::class, 'save_availability']);
Route::post('/trainers/{user}/availability/{availability_id}', [AvailabilityController::class, 'update_availability']);
Route::post('/trainers/{user}/availability/{availability_id}/delete', [AvailabilityController::class, 'remove_availability']);

Route::get('trainers/{user}/upcoming-sessions', [TrainerController::class, 'upcomingSessions']);
Route::get('trainers/{user}/stats', [TrainerController::class, 'stats']);
Route::get('trainers/{user}/session-history', [TrainerController::class, 'sessionHistory']);
Route::get('trainers/{user}/capacity', [TrainerController::class, 'capacity']);
Route::get('trainers/specialisations', [TrainerSpecialisationController::class, 'all']);
