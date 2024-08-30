<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\BlogController;

Route::group(['prefix'=> 'blog', 'cors'], function(){

    /**
     * Load all blog posts.
     */
    Route::get('', [BlogController::class, 'all']);

    /**
     * Load a single blog resource.
     */
    Route::get('{slug}', [BlogController::class, 'single']);
});
