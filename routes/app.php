<?php

use App\Http\Controllers\Mobile\AppController;
use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\BlogController;
use App\Http\Controllers\Mobile\CourseController;
use App\Http\Controllers\Mobile\CustomWorkoutController;
use App\Http\Controllers\Mobile\ExerciseController;
use App\Http\Controllers\Mobile\FocusController;
use App\Http\Controllers\Mobile\HelpController;
use App\Http\Controllers\Mobile\LiveClassController;
use App\Http\Controllers\Mobile\OnboardingController;
use App\Http\Controllers\Mobile\OnDemandController;
use App\Http\Controllers\Mobile\OrderController;
use App\Http\Controllers\Mobile\PackageController;
use App\Http\Controllers\Mobile\PARQController;
use App\Http\Controllers\Mobile\PaymentController;
use App\Http\Controllers\Mobile\PodcastCategoryController;
use App\Http\Controllers\Mobile\PodcastController;
use App\Http\Controllers\Mobile\SessionController;
use App\Http\Controllers\Mobile\StudioController;
use App\Http\Controllers\Mobile\SubscriptionController;
use App\Http\Controllers\Mobile\UserController;
use App\Http\Controllers\Mobile\VirtualCoachingController;
use App\Http\Controllers\Mobile\VlogController;
use App\Http\Controllers\Mobile\WorkoutController;
use App\Http\Controllers\Mobile\ZoomController;

// Main registration.
Route::post('app/register', [AuthController::class, 'registerAccount']);

Route::post('app/register/{step}', [AuthController::class, 'register']);
Route::post('app/login', [AuthController::class, 'login']);
Route::post('app/forgot-password', [AuthController::class, 'resetPassword']);
Route::post('app/validate-bearer', [AuthController::class, 'validateBearer']);

Route::get('app/enabled/on-demand', [AppController::class, 'ondemandEnabled']);
Route::get('app/studios', [StudioController::class, 'index']);


Route::group(['prefix' => 'app', 'middleware' => 'auth:api', 'cors'], function () {

    /**
     * Load PARQs
     */
    Route::get('parq', [PARQController::class, 'all']);
    Route::get('parq/{parq}', [PARQController::class, 'single']);

    Route::post('parq', [PARQController::class, 'savePARQResponse']);

    Route::get('onboarding/promo-pack', [OnboardingController::class, 'getPromoPack']);
    Route::post('onboarding/additional-information', [OnboardingController::class, 'saveAdditionalInformation']);
    Route::post('onboarding/toggle-focus', [OnboardingController::class, 'toggleFocus']);

    Route::get('onboarding/goals', [OnboardingController::class, 'getGoals']);
    Route::post('onboarding/goals', [OnboardingController::class, 'setGoals']);

    Route::get('onboarding/focuses', [OnboardingController::class, 'getFocuses']);
    Route::post('onboarding/focuses', [OnboardingController::class, 'setFocuses']);

    Route::get('onboarding/body-part-focuses', [OnboardingController::class, 'getBodyPartFocuses']);
    Route::post('onboarding/body-part-focuses', [OnboardingController::class, 'setBodyPartFocuses']);

    Route::post('onboarding/fitness-level', [OnboardingController::class, 'setFitnessLevel']);

    Route::post('onboarding/pilates-experience', [OnboardingController::class, 'setPilatesExperience']);

    Route::post('onboarding/parq', [OnboardingController::class, 'savePARQResponse']);

    Route::post('subscription', [SubscriptionController::class, 'store']);
    Route::post('subscription/cancel', [SubscriptionController::class, 'cancelSubscription']);


    Route::get('user', [UserController::class, 'me']);
    Route::patch('user/name', [UserController::class, 'updateName']);
    Route::patch('user/email', [UserController::class, 'updateEmail']);
    Route::patch('user/password', [UserController::class, 'updatePassword']);
    Route::delete('user/account', [UserController::class, 'deleteAccount']);
    Route::patch('member/tracking', [UserController::class, 'updateMemberTrackingTransparency']);

    /**
     * Load the credit packs a user has purchased.
     */
    Route::get('user/my-credit-packs', [UserController::class, 'myCreditPacks']);
    Route::get('user/available-credit-packs', [UserController::class, 'availableCreditPacks']);

    Route::get('user/content-preferences', [UserController::class, 'contentPreferences']);
    Route::patch('user/content-preferences', [UserController::class, 'updateContentPreferences']);

    Route::get('user/notification-preferences', [UserController::class, 'notificationPreferences']);
    Route::patch('user/notification-preferences', [UserController::class, 'updateNotificationPreferences']);

    //Update User Fitness details from Calorie Calculator
    Route::post('user/fitness', [UserController::class, 'updateFitness']);

    // Set users date of birth.
    Route::patch('user/dob', [AuthController::class, 'updateDateOfBirth']);

    // Set a users gender.
    Route::patch('user/gender', [AuthController::class, 'updateGender']);

    // Save push token.
    Route::post('push-token', [UserController::class, 'storePushToken']);

    Route::get('/user/avatar-presets', [UserController::class, 'avatarPresets']);
    Route::patch('/user/avatar-presets', [UserController::class, 'updateAvatarPreset']);

    // List on demand video categories with child videos.
    Route::get('on-demand/categories', [OnDemandController::class, 'categories']);

    Route::get('on-demand/categories/{category:slug}', [OnDemandController::class, 'singleCategory']);

    Route::get('on-demand/continue-watching', [OnDemandController::class, 'continueWatching']);
    Route::get('on-demand/suggested', [OnDemandController::class, 'suggested']);
    Route::get('on-demand/recommended', [OnDemandController::class, 'recommended']);
    Route::get('on-demand/favourites', [OnDemandController::class, 'favourites']);

    // Purchase a class.
    Route::post('on-demand/purchase', [OnDemandController::class, 'purchase']);
    Route::get('on-demand/favourite-ids', [OnDemandController::class, 'favouriteIds']);

    Route::get('on-demand/{ondemand}', [OnDemandController::class, 'single']);
    Route::post('on-demand/{ondemand}/toggle-favourite', [OnDemandController::class, 'toggleFavourite']);

    // List main workouts listing.
    Route::get('workouts', [WorkoutController::class, 'main']);

    // Purchase a workout.
    Route::post('workouts/purchase', [WorkoutController::class, 'purchase']);

    //get single workout
    Route::get('workouts/{workout}', [WorkoutController::class, 'single']);

    // Get the placeholder images for the custom course
    Route::get('workouts/custom/placeholderimages', [CustomWorkoutController::class, 'placeholders']);

    // get a workout category and the subsequent workouts
    Route::get('workouts/category/{category}', [WorkoutController::class, 'category']);

    //get the focuses for the custom workouts
    Route::get('workouts/exercises/sections/{focus}', [ExerciseController::class, 'sections']);

    // Gets the focuses for the workout
    Route::get('focuses', [FocusController::class, 'all']);

    // save the create workout
    Route::post('workouts/custom/save', [CustomWorkoutController::class, 'store']);

    // Add current workout to users fav
    Route::post('workout/favourite', [WorkoutController::class, 'favourite']);

    // Save the stats of a users workout (after completion)
    Route::post('workout/stats', [WorkoutController::class, 'stats']);

    Route::get('virtual-coaching/coaches', [VirtualCoachingController::class, 'trainers']);
    Route::get('virtual-coaching/coaches/{user}/slots/{date?}', [VirtualCoachingController::class, 'slots']);
    Route::post('virtual-coaching/booking', [VirtualCoachingController::class, 'booking']);

    // Fetch a listing of all blog posts.
    Route::get('blogs', [BlogController::class, 'all']);

    // Fetch a listing of all podcasts.
    Route::get('podcasts', [PodcastController::class, 'categories']);

    // Purchase a podcast.
    Route::post('podcasts/purchase', [PodcastController::class, 'purchase']);

    Route::group(['prefix'=> 'virtual-coaching', 'cors'], function(){

        /**
         * Load all upcoming session bookings.
         *
         */
        Route::get('bookings', [VirtualCoachingController::class, 'bookings'])->middleware('auth:api');

        /**
         * Load a single trainer profile.
         */
        Route::get('coaches/{user}', [VirtualCoachingController::class, 'trainerProfile']);

        /**
         * Load the availability calendar for a trainer.
         */
        Route::post('coaches/{user}/availability-calendar', [VirtualCoachingController::class, 'availabilityCalendar']);

        /**
         * Load a session.
         */
        Route::get('session/{session}', [VirtualCoachingController::class, 'singleSession'])->middleware('auth:api');

        /**
         * Cancel a session.
         */
        Route::delete('session/{session}', [VirtualCoachingController::class, 'cancelSession'])->middleware('auth:api');

        /**
         * Create a cart using the bookings.
         */
        Route::post('cart', [VirtualCoachingController::class, 'newCart'])->middleware('auth:api');

        /**
         * Purchase a cart
         */
        Route::post('purchase/{cart}', [VirtualCoachingController::class, 'purchase'])->middleware('auth:api');
    });

    Route::group(['prefix'=> 'packages', 'cors'], function(){

        /**
         * Get all packages
         */
        Route::get('/', [PackageController::class, 'all']);

        /**
         * Get a single member package
         */
        Route::get('/member/{memberpackage}', [PackageController::class, 'singleMemberPackage']);

        /**
         * Get all packages from a specified trainer
         */
        Route::get('trainer/{user}', [PackageController::class, 'trainerPackages']);

        /**
         * Get all logged in users packages
         */
        Route::get('purchased', [PackageController::class, 'purchasedPackages']);
    });


    Route::group(['prefix'=> 'orders', 'cors'], function(){

        /**
         * Get all logged in users packages
         */
        Route::get('myorders', [OrderController::class, 'userOrders']);
    });

    Route::group(['prefix' => 'vlogs', 'cors'], function(){
        /**
         * Get all vlogs
         */
        Route::get('', [VlogController::class, 'all']);

        /**
         * Get a single vlog using slug
         */
        Route::get('{vlog:slug}', [VlogController::class, 'single']);
    });

    Route::group(['prefix' => 'podcasts','middleware' => 'auth:api', 'cors'], function(){
        /**
         * Get all podcast categories
         */
        Route::get('', [PodcastCategoryController::class, 'all']);

        /**
         * Get a single podcast category using the slug
         */
        Route::get('{category:slug}', [PodcastCategoryController::class, 'single']);


        /**
         * get the podcast episode from the DB
         */
        Route::get('/category/{category:slug}/episode/{episode}', [PodcastController::class, 'episode']);

        /**
         * Update the current time of the podcast
         */
        Route::post('/{podcast}/progress/update', [PodcastController::class, 'updateProgress']);

        /**
         * get the users current time and completon of the podcast
         */
        Route::get('/{podcast}/userdata', [PodcastController::class, 'getUserProgress']);
    });

    Route::group(['prefix' => 'courses', 'cors'], function(){
        /**
         * Get all courses
         */
        Route::get('/', [CourseController::class, 'all']);

        /**
         * Get all logged in users course purchases
         */
        Route::get('/purchases', [CourseController::class, 'purchases']);

        /**
         * Get a single course using slug
         */
        Route::get('/{course:slug}', [CourseController::class, 'single']);

        /**
         * Purchase a course for the logged in user using the slug
         */
        Route::get('/{course:slug}/purchase', [CourseController::class, 'purchase']);
    });

    Route::group(['prefix' => 'zoom', 'middleware' => 'auth:api', 'cors'], function() {

        /**
        *
        * Creates a new zoom meeting
        *
        * {
        * "session_id": session.id,
        * "trainer_id": this.$route.params.id,
        * "duration": session.length + 30, (sets the meeting length to 30 mins more than the session length)
        * "regenerate": true / false (if true creates new link regardless if one exists) - for if the trainer acceidentally ends the meeting
        * }
        **/
        Route::post('create', [ZoomController::class, 'store']);

        // Gets the stored zoom link for the session
        Route::get('session/zoomlink/{session}', [SessionController::class, 'singleZoomLink']);

        // Checks that the use has a zoom ID, user = user ID not trainer ID
        Route::get('/user/{trainer}/haszoomid', [ZoomController::class, 'checkTrainerHasZoomID']);

    });

    Route::group(['prefix'=> 'live'], function(){
        Route::get('search', [LiveClassController::class, 'search']);
        Route::get('categories', [LiveClassController::class, 'categories']);
        Route::get('category/{category:slug}', [LiveClassController::class, 'singleCategory']);

        Route::get('class/{liveclass}/stream', [LiveClassController::class, 'openStream']);

        Route::get('class/{liveclass}', [LiveClassController::class, 'singleClass']);
        Route::post('class/{liveclass}/book', [LiveClassController::class, 'bookClass']);
        Route::get('class/{liveclass}/booking', [LiveClassController::class, 'checkBooking']);
        Route::get('class/{liveclass}/bookings', [LiveClassController::class, 'bookingCount']);
        Route::get('class/{liveclass}/average-rating', [LiveClassController::class, 'averageRating']);
        Route::post('class/{liveclass}/toggle-favourite', [LiveClassController::class, 'toggleFavourite']);
        Route::delete('class/{liveclass}/cancel', [LiveClassController::class, 'cancelBooking']);

        Route::get('favourite-ids', [LiveClassController::class, 'favouriteIds']);
        Route::get('bookings', [LiveClassController::class, 'myBookings']);
        Route::get('upcoming/{period}', [LiveClassController::class, 'upcomingClassesByPeriod']);
    });

    Route::post('studios/search', [StudioController::class, 'search']);
    Route::get('studios/{gym}', [StudioController::class, 'single']);
    Route::get('studios/{gym}/reservations/dates', [StudioController::class, 'reservationDates']);
    Route::get('studios/{gym}/reservations/timeslots', [StudioController::class, 'reservationTimeslots']);
    Route::post('studios/{gym}/reservations/timeslots', [StudioController::class, 'createReservation']);

    Route::delete('reservation/{reservation}', [StudioController::class, 'cancelReservation']);

    Route::get('my/reservations', [UserController::class, 'myReservations']);
    Route::post('user/scan-tablet-qr', [UserController::class, 'scanTabletQR']);

    Route::get('help', [HelpController::class, 'index']);
    Route::get('help/featured', [HelpController::class, 'featured']);
    Route::post('help/search', [HelpController::class, 'search']);

    Route::post('payments/payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('payments/payment-intent/{paymentIntent}/complete', [PaymentController::class, 'completePaymentIntent']);
    Route::post('payments/membership/payment-intent', [PaymentController::class, 'createMembershipPaymentIntent']);
    Route::post('payments/capture', [PaymentController::class, 'capturePaymentFromToken']);
    Route::post('payments/card/capture', [PaymentController::class, 'capturePaymentFromCard']);
    Route::post('payments/subscription/capture', [SubscriptionController::class, 'captureSubscription']);

    Route::get('qr', [UserController::class, 'getQRCode']);

    Route::get('memberships', [SubscriptionController::class, 'index']);
    Route::post('memberships/bacs', [SubscriptionController::class, 'purchaseBacsMembership']);
});

require_once('app_v2.php');
