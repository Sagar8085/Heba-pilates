<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\VlogController;

Route::group(['prefix'=> 'vlog', 'cors'], function(){

    /**
     * Load all vlog posts.
     */
    Route::get('', [VlogController::class, 'all']);

    /**
     * Load a single vlog resource.
     */
    Route::get('{slug}', [VlogController::class, 'single']);
});
