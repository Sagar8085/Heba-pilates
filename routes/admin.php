<?php

use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\DoorAccessController;
use App\Http\Controllers\Admin\GymController;
use App\Http\Controllers\Admin\ContractsController;
use App\Http\Controllers\Admin\Member\CreditPackController as MemberCreditPackController;
use App\Http\Controllers\Admin\Member\MembershipController as MemberMembershipController;
use App\Http\Controllers\Admin\MembershipController;

Route::post('admin/login', [AuthController::class, 'login']);
Route::post('admin/invitation', [AuthController::class, 'invitation']);


Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'can:administrator'], 'cors'], function() {

    Route::get('me', [AdminController::class, 'me']);

    // Load ondemand admin specific apis.
    require_once('admin/dashboards.php');

    require('admin/ondemand.php');

    require_once('admin/workouts.php');

    require_once('admin/exercises.php');

    require_once('admin/podcasts.php');

    require_once('admin/trainers.php');

    require_once('admin/blogs.php');

    require_once('admin/vlogs.php');

    require('admin/billing.php');

    require_once('admin/courses.php');

    require_once('admin/packages.php');

    require_once('admin/sessions.php');

    require_once('admin/live.php');

    require_once('admin/gyms.php');

    require_once('admin/leads.php');

    Route::post('contracts', [ContractsController::class, 'uploadContract']);
    Route::post('contracts/upload', [ContractsController::class, 'uploadContractFile']);
    Route::patch('contracts/{contract}', [ContractsController::class, 'editContract']);
    Route::delete('contracts/delete', [ContractsController::class, 'deleteContract']);


    Route::get('search', [SearchController::class, 'search']);

    Route::post('member-search', [MemberController::class, 'memberSearch']);
    Route::post('member-upgrade', [MemberController::class, 'memberUpgrade']);
    Route::post('reserve-slot', [GymController::class, 'reserveSlot']);

    /**
     * Fetch listing of members.
     */
    Route::post('members', [MemberController::class, 'all'])->middleware('can:member-read');
    Route::get('members/tags', [MemberController::class, 'allTags']);
    Route::post('members/tags', [MemberController::class, 'createNewTag']);
    Route::get('members/focuses', [MemberController::class, 'allFocuses']);

    Route::post('members/new', [MemberController::class, 'store']);

    /**
     * Fetch listing of members without pagination.
     */
    Route::get('members/nopagination', [MemberController::class, 'allNonPaginated']);

    /**
     * Load a single member profile.
     */
    Route::get('members/{member}', [MemberController::class, 'single'])->middleware('can:member-read');
    Route::post('members/{member}', [MemberController::class, 'update'])->middleware('can:member-update');
    Route::get('members/{member}/contracts', [MemberController::class, 'listContracts']);
    Route::get('members/{member}/reservations/{type}', [MemberController::class, 'reservations']);
    Route::patch('members/{member}/marketing-preferences', [MemberController::class, 'updateMarketingPreference']);
    Route::post('members/{member}/memberships', [MemberMembershipController::class, 'store'])->name('member.membership.store');
    Route::delete('members/{member}/memberships/{membership}', [MemberController::class, 'deleteMembership']);
    Route::post('members/{member}/memberships/checkout', [MemberMembershipController::class, 'checkoutSession'])->name('member.membership.checkout');
    Route::post('members/{member}/memberships/callback', [MemberMembershipController::class, 'checkoutCallback'])->name('member.membership.callback');
    Route::get('members/{member}/memberships/{type}', [MemberController::class, 'memberships']);
    Route::post('members/{member}/credit-packs', [MemberCreditPackController::class, 'store'])->name('member.pack.store');
    Route::post('members/{member}/credit-packs/checkout', [MemberCreditPackController::class, 'checkoutSession'])->name('member.pack.checkout');
    Route::post('members/{member}/credit-packs/callback', [MemberCreditPackController::class, 'checkoutCallback'])->name('member.pack.callback');
    Route::delete('members/{member}/credit-packs/{creditPackPurchase}', [MemberController::class, 'deleteCreditPack']);
    Route::get('members/{member}/credit-packs/{type}', [MemberController::class, 'creditPackPurchases']);
    Route::get('members/{member}/tags', [MemberController::class, 'memberTags']);
    Route::get('members/{member}/access-log', [MemberController::class, 'accessLog']);
    Route::patch('members/{member}/tags', [MemberController::class, 'saveMemberTags']);
    Route::post('members/{member}/lead', [MemberController::class, 'createLeadProfile']);



    /**
     * Load statistics for member profile.
     */
    Route::get('members/{member}/stats', [MemberController::class, 'stats']);
    Route::get('members/{member}/purchase-history', [MemberController::class, 'purchaseHistory']);
    Route::get('members/{member}/notifications', [MemberController::class, 'notifications']);


    /**
     * Load Members PARQs
     */
    Route::get('members/{user}/parqs', [MemberController::class, 'loadParqs']);
    Route::post('members/{user}/parqs/delete', [MemberController::class, 'deleteParqs']);
    Route::post('members/{user}/parqs/save', [MemberController::class, 'createParqs']);
    /**
     * Send member a push notification.
     */
    Route::post('members/{member}/push-notification', [MemberController::class, 'sendPushNotification']);

     /**
     * Member Notes.
     */
    Route::get('members/{user}/notes', [MemberController::class, 'getNotes']);
    Route::post('members/{user}/notes', [MemberController::class, 'saveNotes']);

    /**
     * Fetch listing of trainers.
     */
    Route::get('trainers', [TrainerController::class, 'all']);

    /**
     * Load a single trainers profile.
     */
    Route::get('trainers/{user}', [TrainerController::class, 'single']);

    /**
     * Update a trainer profile.
     */
    Route::patch('trainers/{user}', [TrainerController::class, 'update']);

    /**
     * Validate and store a new trainer account.
     */
    Route::post('trainers', [TrainerController::class, 'store']);

    /**
     * Upload profile picture for a trainer.
     */
    Route::post('trainers/{user}/upload-image', [TrainerController::class, 'uploadImage']);



    /**
     * Fetch listing of admins.
     */
    Route::get('admins', [AdminController::class, 'all'])->middleware('can:admin-read');

    /**
     * Load a single admin profile.
     */
    Route::get('admins/{user}', [AdminController::class, 'single'])->middleware('can:admin-read');

    /**
     * Validate and store a new admin account.
     */
    Route::post('admins', [AdminController::class, 'store']);

    /**
     * Update admin.
     */
    Route::patch('admins/{user}', [AdminController::class, 'update']);

    /**
     * Upload profile picture for an admin.
     */
    Route::post('admins/{user}/upload-image', [AdminController::class, 'uploadImage']);

    /**
     * Update admin privilege.
     */
    Route::patch('admins/{user}/privilege', [AdminController::class, 'updatePrivilege']);




    Route::get('/door-access/live', [DoorAccessController::class, 'liveLog'])->middleware('can:door-access-dashboard-read');
    Route::get('/door-access/all', [DoorAccessController::class, 'all'])->middleware('can:door-access-dashboard-read');

    Route::get('membership-tiers', [MembershipController::class, 'tiers']);
});
