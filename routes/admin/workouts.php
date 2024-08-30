<?php

use App\Http\Controllers\Admin\WorkoutCategoryController;
use App\Http\Controllers\Admin\WorkoutController;

Route::get('workout/categories', [WorkoutCategoryController::class, 'library']);

Route::get('workout/categories/list', [WorkoutCategoryController::class, 'list']);

Route::get('workout/category/{category}', [WorkoutCategoryController::class, 'single']);

Route::post('workout/category', [WorkoutCategoryController::class, 'store']);

Route::post('workout/category/{category}/update', [WorkoutCategoryController::class, 'update']);

Route::post('workout/category/{category}/delete', [WorkoutCategoryController::class, 'destroy']);

Route::post('workout/category/{category}/upload-image', [WorkoutCategoryController::class, 'uploadImage']);

Route::get('workout/library', [WorkoutController::class, 'library']);

Route::get('workout/library/{workout}', [WorkoutController::class, 'single']);

Route::post('workout/library', [WorkoutController::class, 'store']);

Route::post('workout/{workout}/delete', [WorkoutController::class, 'destroy']);

Route::post('workout/{workout}/update', [WorkoutController::class, 'update']);

Route::post('workout/{workout}/edit/audience', [WorkoutController::class, 'editAudience']);

Route::post('workout/{workout}/customise', [WorkoutController::class, 'customise']);

Route::post('workout/library/{workout}/upload-image', [WorkoutController::class, 'uploadImage']);

Route::get('workout/{workout}/exercises', [WorkoutController::class, 'exercises']);
