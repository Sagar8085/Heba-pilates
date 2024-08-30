<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\Auth\WebPortalAuthController;

/**
 * User Authentication, Registration and Password Resets.
 */
Route::post('login', [WebPortalAuthController::class, 'login'])->name('login');
Route::post('forgot', [WebPortalAuthController::class, 'forgot']);
Route::post('confirm', [WebPortalAuthController::class, 'confirm']);
