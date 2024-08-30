<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebPortal\LiveClassController;

Route::group(['prefix'=> 'live', 'middleware'=>'auth:api', 'cors'], function(){
    Route::get('search', [LiveClassController::class, 'search']);
    Route::get('recommended', [LiveClassController::class, 'recommended']);
    Route::get('categories', [LiveClassController::class, 'categories']);
    Route::get('category/{category:slug}', [LiveClassController::class, 'singleCategory']);

    Route::get('class/{liveclass}', [LiveClassController::class, 'singleClass']);
    Route::get('class/{liveclass}/booking', [LiveClassController::class, 'checkBooking']);
    Route::get('class/{liveclass}/bookings', [LiveClassController::class, 'bookingCount']);
    Route::get('class/{liveclass}/average-rating', [LiveClassController::class, 'averageRating']);
    Route::delete('class/{liveclass}/cancel', [LiveClassController::class, 'cancelBooking']);
    Route::post('class/{liveclass}/book', [LiveClassController::class, 'book']);

    Route::post('class/{liveclass}/toggle-favorite', [LiveClassController::class, 'toggleFavourite']);
    Route::get('favourites', [LiveClassController::class, 'favourites']);
    Route::get('favourites-by-id', [LiveClassController::class, 'favouritesByID']);

    Route::get('bookings', [LiveClassController::class, 'myBookings']);
    Route::get('upcoming/{period}', [LiveClassController::class, 'upcomingClassesByPeriod']);
});
