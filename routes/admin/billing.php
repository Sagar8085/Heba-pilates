<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MembershipController;

Route::group(['middleware' => 'can:order-read'], function() {
    Route::get('billing/credit-packs', [OrderController::class, 'creditPacks']);
    Route::get('billing/credit-packs/{purchase}', [OrderController::class, 'singleCreditPack']);
    Route::patch('billing/credit-packs/{purchase}/expiry', [OrderController::class, 'updateExpiry']);
    Route::get('billing/credit-packs/stats', [OrderController::class, 'creditPackStats']);

    Route::post('billing/orders', [OrderController::class, 'all']);
    Route::get('billing/orders/{order}', [OrderController::class, 'single']);


    Route::post('billing/memberships', [MembershipController::class, 'all']);
    Route::get('billing/memberships/stats', [MembershipController::class, 'membershipStats']);
    Route::get('billing/memberships/{membership}', [MembershipController::class, 'single']);
    Route::patch('billing/memberships/{subscription}', [MembershipController::class, 'update'])->name('billing.memberships.update');
});
