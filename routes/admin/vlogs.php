<?php

use App\Http\Controllers\Admin\VlogController;

/**
 * Fetch library of all vlog posts.
 */
Route::get('vlogs/library', [VlogController::class, 'library']);

/**
 * Store a new vlog resource.
 */
Route::post('vlogs', [VlogController::class, 'store']);

/**
 * Load a single vlog resource.
 */
Route::get('vlogs/{vlog}', [VlogController::class, 'single']);
Route::patch('vlogs/{vlog}', [VlogController::class, 'update']);

/**
 * Upload image for a vlog resource.
 */
Route::post('vlogs/{vlog}/upload-image', [VlogController::class, 'uploadImage']);

/**
 * Upload video for a vlog resource.
 */
Route::post('vlogs/{vlog}/upload-video', [VlogController::class, 'uploadVideo']);
