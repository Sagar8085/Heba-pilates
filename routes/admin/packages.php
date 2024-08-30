<?php

use App\Http\Controllers\Admin\PackageController;

Route::post('packages', [PackageController::class, 'store']);
