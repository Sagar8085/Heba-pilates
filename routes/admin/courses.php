<?php

use App\Http\Controllers\Admin\CourseController;

Route::get('courses/library', [CourseController::class, 'library']);
Route::post('courses/library', [CourseController::class, 'store']);
Route::get('courses/library/{course}', [CourseController::class, 'single']);
Route::patch('courses/library/{course}', [CourseController::class, 'update']);

Route::post('courses/{course}/upload-image', [CourseController::class, 'uploadImage']);
Route::post('courses/{course}/upload-video', [CourseController::class, 'uploadVideo']);

Route::post('courses/{course}/episodes', [CourseController::class, 'storeEpisode']);
Route::post('courses/{course}/episodes/{episode}/upload-image', [CourseController::class, 'uploadEpisodeImage']);
Route::post('courses/{course}/episodes/{episode}/upload-video', [CourseController::class, 'uploadEpisodeVideo']);
