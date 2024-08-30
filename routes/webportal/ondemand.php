<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\OnDemandController;
use App\Http\Controllers\WebPortal\OnDemandCategoryController;

/**
 * Fetch On Demand Data
 */
Route::group(['prefix'=> 'ondemand', 'cors'], function(){
    Route::get('tags', [OnDemandController::class, 'tags'])->middleware('auth:api');
    Route::get('instructors', [OnDemandController::class, 'instructors'])->middleware('auth:api');
    Route::get('suggested', [OnDemandController::class, 'suggested'])->middleware('auth:api');
    Route::get('recommended', [OnDemandController::class, 'recommended'])->middleware('auth:api');
    Route::get('continue-watching', [OnDemandController::class, 'continueWatching'])->middleware('auth:api');
    Route::get('videos', [OnDemandController::class, 'library'])->middleware('auth:api');
    Route::get('categories', [OnDemandCategoryController::class, 'library'])->middleware('auth:api');

    Route::get('categories/{category:slug}', [OnDemandCategoryController::class, 'single'])->middleware('auth:api');

    /**
     * Load a single on demand video.
     */
    Route::get('video/{ondemand}', [OnDemandController::class, 'single'])->middleware('auth:api');
    Route::patch('video/{ondemand}/progress', [OnDemandController::class, 'watchProgress'])->middleware('auth:api');

    Route::post('{ondemand}/purchase', [OnDemandController::class, 'purchase'])->middleware('auth:api');

    Route::post('{ondemand}/toggle-favorite', [OnDemandController::class, 'toggleFavourite'])->middleware('auth:api');
    Route::get('favourites', [OnDemandController::class, 'favourites'])->middleware('auth:api');
    Route::get('favourites-by-id', [OnDemandController::class, 'favouritesByID'])->middleware('auth:api');
});
