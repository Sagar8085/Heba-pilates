<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\WorkoutController;
use App\Http\Controllers\WebPortal\WorkoutCategoryController;

/**
 * Fetch On Demand Data
 */
Route::group(['prefix'=> 'workout', 'cors'], function(){

    Route::get('categories', [WorkoutCategoryController::class, 'library']);

    Route::get('categories/{category:slug}', [WorkoutCategoryController::class, 'single']);

    Route::get('{workout}/exercises', [WorkoutController::class, 'exercises']);

    Route::post('{workout}/purchase', [WorkoutController::class, 'purchase'])->middleware('auth:api');

    Route::group(['prefix'=> 'save'], function(){

        Route::post('favourite', [WorkoutController::class, 'favourite'])->middleware('auth:api');

        Route::post('stats', [WorkoutController::class, 'stats'])->middleware('auth:api');
    });
});
