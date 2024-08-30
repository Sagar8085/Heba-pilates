<?php

use App\Http\Controllers\Admin\SessionController;

Route::get('sessions', [SessionController::class, 'all']);
Route::get('sessions/{session}', [SessionController::class, 'single']);
Route::patch('sessions/{session}/cancel', [SessionController::class, 'cancel']);
Route::patch('sessions/{session}/accept', [SessionController::class, 'accept']);
Route::patch('sessions/{session}/decline', [SessionController::class, 'decline']);
