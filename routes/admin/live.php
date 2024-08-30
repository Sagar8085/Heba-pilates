<?php

use App\Http\Controllers\Admin\LiveClassController;
use App\Http\Controllers\Admin\LiveClassCategoryController;

Route::get('live-classes', [LiveClassController::class, 'library']);
Route::post('live-classes', [LiveClassController::class, 'store']);
Route::get('live-classes/instructors', [LiveClassController::class, 'instructors']);
Route::get('live-classes/categories', [LiveClassCategoryController::class, 'index']);
Route::post('live-classes/categories', [LiveClassCategoryController::class, 'store']);

Route::get('live-classes/{liveclass}', [LiveClassController::class, 'single']);
Route::get('live-classes/category/{category}', [LiveClassCategoryController::class, 'single']);
Route::post('live-classes/category/{category}', [LiveClassCategoryController::class, 'update']);
Route::post('live-classes/category/{category}/upload-image', [LiveClassCategoryController::class, 'uploadImage']);
