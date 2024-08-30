<?php

use App\Http\Controllers\Admin\DashboardPenetrationController;
use App\Http\Controllers\Admin\ClassPenetrationController;
use App\Http\Controllers\Admin\Dashboards\DashboardRevenueController;
use App\Http\Controllers\Admin\Dashboards\DashboardSessionController;
use App\Http\Controllers\Admin\Dashboards\WorkoutPenetrationController;
use App\Http\Controllers\Admin\Dashboards\PodcastPenetrationController;
use App\Http\Controllers\Admin\Dashboards\UserDashboardController;
use App\Http\Controllers\Admin\Dashboards\LiveClassPenetrationController;
use App\Http\Controllers\Admin\Dashboards\MainDashboardController;

Route::get('dashboard/main/on-demand', [MainDashboardController::class, 'ondemandStats']);
Route::get('dashboard/main/live', [MainDashboardController::class, 'liveStats']);

Route::get('dashboard/penetration/classes', [DashboardPenetrationController::class, 'classes']);
Route::get('dashboard/penetration/classes/{category}/top-classes', [ClassPenetrationController::class, 'topClasses']);
Route::post('dashboard/penetration/classes/{category}/members', [ClassPenetrationController::class, 'categoryMemberList']);
Route::get('dashboard/penetration/classes/{category}/30-day-views', [ClassPenetrationController::class, 'previousThirtyDays']);

Route::get('dashboard/penetration/live', [DashboardPenetrationController::class, 'live']);
Route::get('dashboard/penetration/live/{category}/30-day-bookings', [LiveClassPenetrationController::class, 'previousThirtyDays']);
Route::get('dashboard/penetration/live/{category}/top-classes', [LiveClassPenetrationController::class, 'topClasses']);
Route::get('dashboard/penetration/live/{category}/members', [LiveClassPenetrationController::class, 'categoryMemberList']);

/**
 * Revenue Dashboard.
 */
Route::get('dashboard/revenue/last-twelve-months', [DashboardRevenueController::class, 'lastTwelveMonths']);
Route::get('dashboard/revenue/trainers', [DashboardRevenueController::class, 'trainers']);
Route::get('dashboard/revenue/top-streams', [DashboardRevenueController::class, 'topStreams']);

/**
 * Sessions Dashboard.
 */
Route::get('dashboard/sessions/stats', [DashboardSessionController::class, 'stats']);
Route::get('dashboard/sessions/bookings', [DashboardSessionController::class, 'bookings']);
Route::get('dashboard/sessions/cancellations', [DashboardSessionController::class, 'cancellations']);


Route::get('dashboard/penetration/demographics/age', [DashboardPenetrationController::class, 'loadAgeDemographics']);
Route::get('dashboard/penetration/demographics/gender', [DashboardPenetrationController::class, 'loadGenderDemographics']);

Route::get('dashboard/penetration/popularity', [DashboardPenetrationController::class, 'loadPopularityGraph']);

Route::post('dashboard/penetration/members', [DashboardPenetrationController::class, 'loadMembers']);


/**
 * Load user dashboard stats.
 */
Route::get('dashboard/users/stats', [UserDashboardController::class, 'stats']);
