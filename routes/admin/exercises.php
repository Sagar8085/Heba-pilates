<?php

use App\Http\Controllers\Admin\ExerciseCategoryController;
use App\Http\Controllers\Admin\ExerciseController;
use App\Http\Controllers\Admin\FocusController;
use App\Http\Controllers\Admin\IntensityMETController;

/**
 * Fetch listing of exercise categories.
 */
Route::get('exercise/categories', [ExerciseCategoryController::class, 'all']);

Route::get('exercise/category/{category}', [ExerciseCategoryController::class, 'single']);

Route::post('exercise/category/{category}/update', [ExerciseCategoryController::class, 'update']);

/**
 * Store a new category.
 */
Route::post('exercise/category', [ExerciseCategoryController::class, 'store']);

Route::post('exercise/category/{category}/delete', [ExerciseCategoryController::class, 'destroy']);

/**
 * Upload image for an exercise category.
 */
Route::post('exercise/category/{category}/upload-image', [ExerciseCategoryController::class, 'uploadImage']);

/**
 * Fetch listing of exercise videos.
 */
Route::get('exercise/library', [ExerciseController::class, 'library']);

Route::get('exercise/library/{exercise}', [ExerciseController::class, 'single']);

Route::get('exercise/sections/{focus}', [ExerciseController::class, 'sections']);

Route::get('exercise/sections', [ExerciseController::class, 'sections']);

Route::post('exercise/library', [ExerciseController::class, 'store']);

Route::post('exercise/library/{exercise}/upload-video', [ExerciseController::class, 'uploadVideo']);

Route::post('exercise/library/{exercise}/upload-image', [ExerciseController::class, 'uploadImage']);

Route::post('exercise/{exercise}/delete', [ExerciseController::class, 'destroy']);

Route::post('exercise/{exercise}/update', [ExerciseController::class, 'update']);

/**
* Fetch all Focuses for the exercises
*/
Route::get('exercise/focuses', [FocusController::class, 'index']);

Route::get('exercise/sections/{focus}', [ExerciseController::class, 'exercisesWithSection']);

Route::get('exercise/intensities', [IntensityMETController::class, 'index']);
