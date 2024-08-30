<?php
use App\Http\Controllers\Tablet\QRController;

Route::get('tablet/qr', [QRController::class, 'generateLoginQR']);
Route::get('tablet/qr/{identifier}', [QRController::class, 'checkLoginQR']);
