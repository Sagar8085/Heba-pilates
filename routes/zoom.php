<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Zoom\ZoomController;
use App\Http\Controllers\Admin\SessionController;


Route::group(['prefix' => 'zoom', 'middleware' => 'auth:api', 'cors'], function() {

    Route::post('create', [ZoomController::class, 'store']);

    Route::post('session/storelink', [SessionController::class, 'storeZoomLink']);

    Route::get('session/zoomlink/{session}', [SessionController::class, 'singleZoomLink']);

    Route::post('user/storeid', [ZoomController::class, 'storeUserId']);

    Route::get('user/{trainer}/id', [ZoomController::class, 'getId']);
});
