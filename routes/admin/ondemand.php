<?php

use App\Http\Controllers\Admin\OnDemandCategoryController;
use App\Http\Controllers\Admin\OnDemandController;
use App\Http\Controllers\Admin\OnDemandTagController;

Route::group(['middleware' => 'can:class-read'], function() {

    /**
     * Fetch listing of on demand videos.
     */
    Route::get('on-demand/library', [OnDemandController::class, 'library']);

    /**
     * Store a new video resource.
     */
    Route::post('on-demand/library', [OnDemandController::class, 'store']);

    /**
     * Load a single on demand resource.
     */
    Route::get('on-demand/library/{ondemand}', [OnDemandController::class, 'single']);

    /**
     * Update a single on demand resource.
     */
    Route::patch('on-demand/library/{ondemand}', [OnDemandController::class, 'update']);

    Route::delete('on-demand/library/{ondemand}', [OnDemandController::class, 'destroy']);

    /**
     * Upload image for a on demand resource.
     */
    Route::post('on-demand/library/{ondemand}/upload-image', [OnDemandController::class, 'uploadImage']);

    /**
     * Upload video for a on demand resource.
     */
    Route::post('on-demand/library/{ondemand}/upload-video', [OnDemandController::class, 'uploadVideo']);

    /**
     * Fetch listing of on demand categories.
     */
    Route::get('on-demand/categories', [OnDemandCategoryController::class, 'all']);

    Route::get('on-demand/categories/{category}', [OnDemandCategoryController::class, 'single']);

    Route::get('on-demand/categories/order/edit', [OnDemandCategoryController::class, 'orderList']);
    Route::patch('on-demand/categories/order/update', [OnDemandCategoryController::class, 'updateOrder']);


    /**
     * Store a new category.
     */
    Route::post('on-demand/category', [OnDemandCategoryController::class, 'store']);

    Route::post('on-demand/category/{category}/update', [OnDemandCategoryController::class, 'update']);
    Route::get('on-demand/categories/{category}/order-list', [OnDemandController::class, 'orderList']);
    Route::patch('on-demand/categories/{category}/order-list', [OnDemandController::class, 'updateOrder']);

    /**
     * Upload image for a on demand category.
     */
    Route::post('on-demand/category/{category}/upload-image', [OnDemandCategoryController::class, 'uploadImage']);

    /**
     * Manage OnDemand Tags
     */
    Route::get('on-demand/tags', [OnDemandTagController::class, 'index'])->name('ondemand.tags.index');
    Route::get('on-demand/tags/{ondemand}', [OnDemandTagController::class, 'show'])->name('ondemand.tags.show');
    Route::post('on-demand/tags', [OnDemandTagController::class, 'store'])->name('ondemand.tags.store');
    Route::patch('on-demand/tags/{ondemand}', [OnDemandTagController::class, 'update'])->name('ondemand.tags.update');
});
