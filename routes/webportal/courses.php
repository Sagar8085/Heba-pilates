<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\CourseController;

Route::group(['prefix'=> 'courses', 'cors'], function(){
    Route::get('', [CourseController::class, 'all']);
    Route::get('/{slug}', [CourseController::class, 'single']);
    Route::post('{slug}/purchase', [CourseController::class, 'purchase'])->middleware('auth:api');
});
