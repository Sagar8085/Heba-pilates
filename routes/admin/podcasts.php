<?php

use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\PodcastCategoryController;

/**
 * Library of all podcasts.
 */
Route::get('podcasts', [PodcastController::class, 'all']);

/**
 * Store a new podcast resource.
 */
Route::post('podcasts/library', [PodcastController::class, 'store']);

/**
 * Load a single podcast resource.
 */
Route::get('podcasts/library/{podcast}', [PodcastController::class, 'single']);

/**
 * Upload image for a podcast resource.
 */
Route::post('podcasts/library/{podcast}/upload-image', [PodcastController::class, 'uploadImage']);

/**
 * Upload video for a podcast resource.
 */
Route::post('podcasts/library/{podcast}/upload-video', [PodcastController::class, 'uploadVideo']);

/**
 * Update main podcast resource.
 */
Route::patch('podcasts/library/{podcast}', [PodcastController::class, 'update']);

/**
 * Update the status of a podcast.
 */
Route::patch('podcasts/library/{podcast}/update-status', [PodcastController::class, 'updateStatus']);

/**
 * List of podcast categories.
 */
Route::get('podcasts/categories', [PodcastCategoryController::class, 'all']);


/**
 * Store a new category.
 */
Route::post('podcasts/category', [PodcastCategoryController::class, 'store']);

/**
 * Load a single podcast category resource.
 */
Route::get('podcasts/category/{category}', [PodcastCategoryController::class, 'single']);

/**
 * Update a single podcast category resource.
 */
Route::patch('podcasts/category/{category}', [PodcastCategoryController::class, 'update']);

/**
 * Delete a single podcast category resource.
 */
Route::delete('podcasts/category/{category}', [PodcastCategoryController::class, 'destroy']);

/**
 * Upload image for a on demand category.
 */
Route::post('podcasts/category/{category}/upload-image', [PodcastCategoryController::class, 'uploadImage']);
