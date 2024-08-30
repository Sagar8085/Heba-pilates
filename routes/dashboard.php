<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Dashboard\CreditPackController;
use App\Http\Controllers\Dashboard\SubscriptionTypeController;
use App\Http\Controllers\Dashboard\TotalRevenueController;

Route::get('revenue/total', TotalRevenueController::class)->name('revenue.index');