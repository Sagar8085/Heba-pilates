<?php

use App\Http\Controllers\Admin\BlogController;

/**
 * Fetch library of all blog posts.
 */
Route::get('blogs/library', [BlogController::class, 'library']);

/**
 * Store a new blog resource.
 */
Route::post('blogs', [BlogController::class, 'store']);

/**
 * Load a single blog resource.
 */
Route::get('blogs/library/{blog}', [BlogController::class, 'single']);

/**
 * Update main blog resource.
 */
Route::patch('blogs/library/{blog}', [BlogController::class, 'update']);

/**
 * Upload image for a blog resource.
 */
Route::post('blogs/{blog}/upload-image', [BlogController::class, 'uploadImage']);

/**
 * Delete a blog resource.
 */
Route::delete('blogs/library/{blog}', [BlogController::class, 'destroy']);
