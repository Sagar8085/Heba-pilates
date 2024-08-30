<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\CustomWorkoutController;
use App\Http\Controllers\WebPortal\UserController;


/**
 * Fetch custom workout data
 */
Route::group(['prefix'=> 'customworkout', 'middleware'=>'auth:api', 'cors'], function(){

    Route::post('save', [CustomWorkoutController::class, 'store']);

    Route::get('placeholders', [CustomWorkoutController::class, 'placeholders']);

    Route::get('all', [UserController::class, 'customWorkout']);

});
