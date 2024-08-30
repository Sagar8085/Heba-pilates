<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Dashboard\CreditPackController;
use App\Http\Controllers\Dashboard\SubscriptionTypeController;

Route::get('subscriptionTypes', SubscriptionTypeController::class)->name('subscription-type.index');

Route::get('creditPackTypes', CreditPackController::class)->name('credit-pack.index');

Route::resource('product', ProductController::class);

Route::get('guest', UserController::class)->name('guest.index');