<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\PodcastController;
use App\Http\Controllers\WebPortal\PodcastCategoryController;

Route::group(['prefix'=> 'podcasts', 'cors'], function(){

    /**
     * Load all podcast categories.
     */
    Route::get('categories', [PodcastCategoryController::class, 'all']);

    /**
     * Load a single podcast category and all podcasts within it.
     */
    Route::get('category/{slug}', [PodcastCategoryController::class, 'single']);

    Route::post('{podcast}/purchase', [PodcastController::class, 'purchase'])->middleware('auth:api');


    /**
     * get the podcast episode from the DB
     */
    Route::get('/category/{category:slug}/episode/{episode}', [PodcastController::class, 'episode']);

    /**
     * Update the current time of the podcast
     */
    Route::post('/{podcast}/progress/update', [PodcastController::class, 'updateProgress'])->middleware('auth:api');

    /**
     * get the users current time and completon of the podcast
     */
    Route::get('/{podcast}/userdata', [PodcastController::class, 'getUserProgress'])->middleware('auth:api');
});
