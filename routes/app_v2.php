<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Mobile\v2\AuthController;

Route::post('app/v2/register', [AuthController::class, 'registerAccount']);
