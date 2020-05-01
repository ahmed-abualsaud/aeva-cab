<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/signup' , 'RiderController@signup');
Route::post('/user/login' , 'RiderController@login');
Route::post('/driver/signup' , 'DriverController@signup');
Route::post('/driver/login' , 'DriverController@login');

Route::group(['middleware' => ['auth:user']], function () {
    Route::group(['prefix' => 'user'], function () { 
        Route::post('/update/location', 'RiderController@update_location');
        Route::get('/details', 'RiderController@details');
        Route::get('/services', 'RiderController@services');
        Route::post('/rate/provider', 'RiderController@rate_provider');
        Route::post('/send/request', 'RiderController@send_request');
        Route::post('/cancel/request', 'RiderController@cancel_request');
        Route::get('/request/check', 'RiderController@request_status_check');
        Route::get('/show/providers', 'RiderController@show_providers');
        Route::post('/update/request', 'RiderController@modifiy_request');
        Route::get('/trips', 'RiderController@trips');
        Route::get('/upcoming/trips', 'RiderController@upcoming_trips');
        Route::get('/trip/details', 'RiderController@trip_details');
        Route::get('/upcoming/trip/details', 'RiderController@upcoming_trip_details');
        Route::post('/payment', 'PaymentController@payment');
        Route::post('/add/money', 'PaymentController@add_money');
        Route::get('/estimated/fare', 'RiderController@estimated_fare');
        Route::get('/help', 'RiderController@help_details');
        Route::get('/promocodes', 'RiderController@promocodes');
        Route::post('/promocode/add', 'RiderController@add_promocode');
        Route::resource('card', 'CardController');
        Route::resource('location', 'FavouriteLocationController');
        Route::get('/wallet/passbook', 'RiderController@wallet_passbook');
        Route::get('/promo/passbook', 'RiderController@promo_passbook');
    });
});

Route::group(['middleware' => ['auth:driver']], function () {
    Route::group(['prefix' => 'provider'], function () {
        Route::get('/target', 'DriverController@target');
        Route::resource('trip', 'TripController');
        Route::post('cancel', 'TripController@cancel');
        Route::post('summary', 'TripController@summary');
        Route::get('help', 'TripController@help_details');
        Route::group(['prefix' => 'profile'], function () {
            Route::get ('/', 'DriverController@index');
            Route::post('/location', 'DriverController@location');
            Route::post('/available', 'DriverController@available');
        });
        Route::group(['prefix' => 'trip'], function () {
            Route::post('{id}', 'TripController@accept');
            Route::post('{id}/rate', 'TripController@rate');
            Route::post('{id}/message' , 'TripController@message');
            Route::post('{id}/calculate','TripController@calculate_distance');
        });
        Route::group(['prefix' => 'requests'], function () {
            Route::get('/upcoming' , 'TripController@scheduled');
            Route::get('/history', 'TripController@history');
            Route::get('/history/details', 'TripController@history_details');
            Route::get('/upcoming/details', 'TripController@upcoming_details');
        });
    });
});