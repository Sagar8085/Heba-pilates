<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\ExerciseController;


/**
 * Fetch Exercise data
 */
Route::group(['prefix'=> 'exercise', 'middleware'=>'auth:api', 'cors'], function(){

    Route::get('sections/{focus}', [ExerciseController::class, 'sections']);

    Route::get('focus', [ExerciseController::class, 'focuses']);
});
